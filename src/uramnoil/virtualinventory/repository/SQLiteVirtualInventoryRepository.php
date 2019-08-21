<?php


namespace uramnoil\virtualinventory\repository;


use Closure;
use pocketmine\inventory\Inventory;
use pocketmine\IPlayer;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Server;
use pocketmine\utils\Utils;
use uramnoil\virtualinventory\inventory\factory\VirtualChestInventoryFactory;
use uramnoil\virtualinventory\inventory\factory\VirtualDoubleChestInventoryFactory;
use uramnoil\virtualinventory\inventory\factory\VirtualInventoryFactory;
use uramnoil\virtualinventory\inventory\VirtualInventory;
use uramnoil\virtualinventory\repository\dao\SQLite3VirtualInventoryDAO;
use uramnoil\virtualinventory\repository\dao\VirtualInventoryDAO;
use uramnoil\virtualinventory\task\TransactionTask;
use uramnoil\virtualinventory\VirtualInventoryPlugin;

use function uramnoil\virtualinventory\repository\extension\itemsToRaw;
use function uramnoil\virtualinventory\repository\extension\rawToItems;

class SQLiteVirtualInventoryRepository implements VirtualInventoryRepository {
	/** @var VirtualInventoryFactory[] */
	private $factories = [];
	/** @var Inventory[] */
	private $cachedInventories = [];
	/** @var VirtualInventoryPlugin  */
	private $plugin;
	/** @var VirtualInventoryDAO */
	private $dao;

	public function __construct(VirtualInventoryPlugin $plugin) {
		$this->plugin = $plugin;

		$this->factories[SQLite3VirtualInventoryDAO::INVENTORY_TYPE_CHEST] = new VirtualChestInventoryFactory($this);
		$this->factories[SQLite3VirtualInventoryDAO::INVENTORY_TYPE_DOUBLE_CHEST] = new VirtualDoubleChestInventoryFactory($this);
	}

	public function save(VirtualInventory $inventory) : void {
		$id = $inventory->getId();
		$itemsRaw = itemsToRaw($inventory->getContents(true));
		$this->dao->update($id, $itemsRaw);
	}


	public function new(IPlayer $owner, Closure $onDone) : void {
		Utils::validateCallableSignature(function(VirtualInventory $inventory) : void{}, $onDone);


	}

	public function findByOwner(IPlayer $owner, Closure $onDone) : void {
		// TODO: Implement findByOwner() method.
		WindowTypes::
	}

	public function findById(int $id, Closure $onDone) : void {
		if(isset($this->cachedInventories[$id])) {
			return $onDone($this->cachedInventories[$id]);
		}

		$task = new TransactionTask(function() use($id) : ?VirtualInventory {
			$inventoryRaw = $this->dao->findById($id);
			if($inventoryRaw !== null) return null;
			$owner = Server::getInstance()->getOfflinePlayer($inventoryRaw['owner_name']);
			$id = $inventoryRaw['inventory_id'];
			$inventory = $this->factories[$inventoryRaw['inventory_type']]->createFrom($id, $owner);
			$inventory->setContents(rawToItems($inventoryRaw['items']));
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
		// TODO: Implement close() method.
	}
}