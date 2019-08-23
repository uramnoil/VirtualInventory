<?php


namespace uramnoil\virtualinventory\repository;


use Closure;
use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Utils;
use uramnoil\virtualinventory\inventory\factory\VirtualChestInventoryFactory;
use uramnoil\virtualinventory\inventory\factory\VirtualDoubleChestInventoryFactory;
use uramnoil\virtualinventory\inventory\factory\VirtualInventoryFactory;
use uramnoil\virtualinventory\inventory\VirtualInventory;
use uramnoil\virtualinventory\repository\dao\virtualinventory\SQLiteVirtualInventoryDAO;
use uramnoil\virtualinventory\repository\dao\virtualinventory\VirtualInventoryDAO;
use uramnoil\virtualinventory\repository\extension\InventoryConverterTrait;
use uramnoil\virtualinventory\repository\extension\SchedulerTrait;
use uramnoil\virtualinventory\task\TransactionTask;
use uramnoil\virtualinventory\VirtualInventoryPlugin;
use function array_merge;

class SQLiteVirtualInventoryRepository implements VirtualInventoryRepository {
	use InventoryConverterTrait;
	use SchedulerTrait;

	/** @var VirtualInventoryFactory[] */
	private $factories = [];
	/** @var VirtualInventory[] */
	private $cachedInventories = [];
	/** @var VirtualInventoryPlugin  */
	private $plugin;
	/** @var VirtualInventoryDAO */
	private $dao;

	public function __construct(PluginBase $plugin) {	//OPTIMIZE	ファイルの保存場所さえ得られればいい
		$this->plugin = $plugin;

		$this->factories[SQLiteVirtualInventoryDAO::INVENTORY_TYPE_CHEST]        = new VirtualChestInventoryFactory($this);
		$this->factories[SQLiteVirtualInventoryDAO::INVENTORY_TYPE_DOUBLE_CHEST] = new VirtualDoubleChestInventoryFactory($this);
	}

	public function close() : void {
		$this->dao->close();
	}

	public function save(VirtualInventory $inventory, ?Closure $onDone) : void {
		$onDone !== null ?
			Utils::validateCallableSignature(function(object $noUse) : void{}, $onDone)
			: $onDone = function(object $onUse) : void {};

		$task = new TransactionTask(function() use ($inventory) : ?object {
			$this->dao->update($inventory->getId(), $this->itemsToRaw($inventory->getContents(true)));
			return null;
		}, $onDone);

		$this->submitTask($task);
	}


	public function new(IPlayer $owner, int $inventoryType = InventoryIds::INVENTORY_TYPE_CHEST, ?Closure $onDone = null) : void {

		$task = new TransactionTask(function() use ($owner, $inventoryType) : VirtualInventory {
			$inventoryRaw = $this->dao->create($owner->getName(), $inventoryType);	//OPTIMIZE	ルールが散らばってる
			return $this->factories[$inventoryType]->createFrom($inventoryRaw['inventory_id'], $owner);
		}, $onDone ?? function(VirtualInventory $inventory) : void {});

		$this->submitTask($task);
	}

	public function findByOwner(IPlayer $owner, Closure $onDone) : void {
		Utils::validateCallableSignature(function(array $inventories) : void{}, $onDone);

		$task = new TransactionTask(function() use($owner) : array {
			$idsNotIn = [];
			$cachedInventories = [];
			foreach($this->cachedInventories as $inventory) {
				if($inventory->getOwner()->getName() === $owner->getName()) {
					$cachedInventories[$inventory->getId()] = $inventory;
					$idsNotIn[$inventory->getId()] = $inventory;
				}
			}
			$inventoryRaws = $this->dao->findByOwner($owner->getName(), [VirtualInventoryDAO::OPTION_IDS_NOT_IN => $idsNotIn]);

			$inventories = [];

			foreach($inventoryRaws as $inventoryRaw) {
				$inventory = $this->factories[$inventoryRaw['inventory_type']]
					->createFrom(
						$inventoryRaw['inventory_id'],
						Server::getInstance()->getOfflinePlayer($inventoryRaw['owner_name'])
					);
				$inventory->setContents($this->rawToItems($inventoryRaw['items']));
				$inventories[$inventory->getId()] = $inventory;
			}

			return array_merge($cachedInventories, $inventories);
		}, $onDone);

		$this->submitTask($task);
	}

	public function findById(int $id, Closure $onDone) : void {
		Utils::validateCallableSignature(function(?VirtualInventory $inventory) : void{}, $onDone);

		if(isset($this->cachedInventories[$id])) {
			return $onDone($this->cachedInventories[$id]);
		}

		$task = new TransactionTask(function() use($id) : ?VirtualInventory {
			$inventoryRaw = $this->dao->findById($id);
			if($inventoryRaw !== null) return null;

			$inventory = $this->factories[$inventoryRaw['inventory_type']]
				->createFrom(
					$inventoryRaw['inventory_id'],
					Server::getInstance()->getOfflinePlayer($inventoryRaw['owner_name'])
				);
			$inventory->setContents($this->rawToItems($inventoryRaw['items']));
			return $inventory;
		}, $onDone);

		$this->submitTask($task);
	}

	public function delete(VirtualInventory $inventory, ?Closure $onDone) : void {
		isset($onDone) ?
			Utils::validateCallableSignature(function(?object $result) : void{}, $onDone)
			: $onDone = function(?object $result) : void {};

		$task = new TransactionTask(function() use($inventory) : void {
			$this->dao->delete($inventory->getId());
		}, function(?object $result) use($inventory, $onDone) : void {
			$inventory->onDelete();
			unset($this->cachedInventories[$inventory->getId()]);
			$onDone($result);
		});

		$this->submitTask($task);
	}
}