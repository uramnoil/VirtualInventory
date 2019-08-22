<?php


namespace uramnoil\virtualinventory\repository;


use Closure;
use pocketmine\IPlayer;
use pocketmine\Server;
use pocketmine\utils\Utils;
use uramnoil\virtualinventory\inventory\factory\VirtualChestInventoryFactory;
use uramnoil\virtualinventory\inventory\factory\VirtualDoubleChestInventoryFactory;
use uramnoil\virtualinventory\inventory\factory\VirtualInventoryFactory;
use uramnoil\virtualinventory\inventory\VirtualInventory;
use uramnoil\virtualinventory\repository\dao\virtualinventory\SQLite3VirtualInventoryDAO;
use uramnoil\virtualinventory\repository\dao\virtualinventory\VirtualInventoryDAO;
use uramnoil\virtualinventory\repository\extension\InventoryConverterTrait;
use uramnoil\virtualinventory\repository\extension\SchedulerTrait;
use uramnoil\virtualinventory\task\TransactionTask;
use uramnoil\virtualinventory\VirtualInventoryPlugin;
use function strtolower;

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

	public function __construct(VirtualInventoryPlugin $plugin) {	//OPTIMIZE	ファイルの保存場所さえ得られればいい
		$this->plugin = $plugin;

		$this->factories[SQLite3VirtualInventoryDAO::INVENTORY_TYPE_CHEST] = new VirtualChestInventoryFactory($this);
		$this->factories[SQLite3VirtualInventoryDAO::INVENTORY_TYPE_DOUBLE_CHEST] = new VirtualDoubleChestInventoryFactory($this);
	}

	public function save(VirtualInventory $inventory) : void {
		$id = $inventory->getId();
		$itemsRaw = $this->itemsToRaw($inventory->getContents(true));
		$this->dao->update($id, $itemsRaw);
	}


	public function new(IPlayer $owner, int $inventoryType = InventoryIds::INVENTORY_TYPE_CHEST, ?Closure $onDone = null) : void {
		Utils::validateCallableSignature(function(VirtualInventory $inventory) : void{}, $onDone);

		$task = new TransactionTask(function() use ($owner, $inventoryType) : VirtualInventory {
			$inventoryRaw = $this->dao->create(strtolower($owner->getName()), $inventoryType);	//OPTIMIZE	ルールが散らばってる
			return $this->factories[$inventoryType]->createFrom($inventoryRaw['inventory_id'], $owner);
		}, $onDone);

		$this->submitTask($task);
	}

	public function findByOwner(IPlayer $owner, Closure $onDone) : void {
		$notIn = [];
		foreach($this->cachedInventories as $inventory) {
			if($inventory->getOwner()->getName() === $owner->getName()) {
				$notIn[$inventory->getId()] = $inventory;
			}
		}

		$task = new TransactionTask(function() use($id, $notIn) : ?VirtualInventory {
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
	}

	public function findById(int $id, Closure $onDone) : void {
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

		Server::getInstance()->getAsyncPool()->submitTask($task);
	}

	public function delete(VirtualInventory $inventory) : void {
		$inventory->onDelete();
		$id = $inventory->getId();

		$task = new TransactionTask(function() use($id) : void {
			$this->dao->delete($id);
		}, function(?object $result) use($id) : void {
			unset($this->cachedInventories[$id]);
		});

		Server::getInstance()->getAsyncPool()->submitTask($task);
	}

	public function close() : void {
		$this->dao->close();
	}
}