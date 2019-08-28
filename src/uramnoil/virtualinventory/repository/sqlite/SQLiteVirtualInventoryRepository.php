<?php


namespace uramnoil\virtualinventory\repository\sqlite;


use Closure;
use pocketmine\IPlayer;
use SQLite3;
use uramnoil\virtualinventory\inventory\factory\PerpetuatedVirtualChestInventoryFactory;
use uramnoil\virtualinventory\inventory\factory\PerpetuatedVirtualDoubleChestInventoryFactory;
use uramnoil\virtualinventory\inventory\factory\PerpetuatedVirtualInventoryFactory;
use uramnoil\virtualinventory\inventory\PerpetuatedVirtualInventory;
use uramnoil\virtualinventory\repository\dao\VirtualInventoryDAO;
use uramnoil\virtualinventory\repository\DatabaseException;
use uramnoil\virtualinventory\repository\extension\InventoryConverterTrait;
use uramnoil\virtualinventory\repository\InventoryIds;
use uramnoil\virtualinventory\repository\sqlite\dao\SQLiteVirtualInventoryDAO;
use uramnoil\virtualinventory\repository\VirtualInventoryRepository;
use uramnoil\virtualinventory\VirtualInventoryPlugin;
use function array_merge;

class SQLiteVirtualInventoryRepository implements VirtualInventoryRepository {
	use InventoryConverterTrait;

	/** @var PerpetuatedVirtualInventoryFactory[] */
	private $factories = [];
	/** @var PerpetuatedVirtualInventory[] */
	private $cachedInventories = [];
	/** @var VirtualInventoryPlugin  */
	private $plugin;
	/** @var SQLiteVirtualInventoryDAO */
	private $dao;

	public function __construct(SQLite3 $db) {

		$this->dao = new SQLiteVirtualInventoryDAO($db);
		$this->factories[InventoryIds::INVENTORY_TYPE_CHEST]        = new PerpetuatedVirtualChestInventoryFactory($this);
		$this->factories[InventoryIds::INVENTORY_TYPE_DOUBLE_CHEST] = new PerpetuatedVirtualDoubleChestInventoryFactory($this);
	}

	public function close() : void {
		$this->dao->close();
	}

	public function save(PerpetuatedVirtualInventory $inventory) : void {
		$this->dao->update($this->inventoryToRaw($inventory));
	}


	public function new(IPlayer $owner, int $inventoryType = InventoryIds::INVENTORY_TYPE_CHEST, ?Closure $onDone = null) : PerpetuatedVirtualInventory {
		$inventoryRaw = $this->dao->create($owner->getName(), $inventoryType);
		return $this->factories[$inventoryType]->createFrom($inventoryRaw['inventory_id'], $owner);
	}

	public function findByOwner(IPlayer $owner) : array{
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
			$inventory = $this->rawToInventory($inventoryRaw);
			$inventories[$inventory->getId()] = $inventory;
		}

		return array_merge($cachedInventories, $inventories);
	}

	public function findById(int $id) : PerpetuatedVirtualInventory{
		if(isset($this->cachedInventories[$id])) return $this->cachedInventories[$id];

		$inventoryRaw = $this->dao->findById($id);
		if($inventoryRaw !== null) return null;

		$inventory = $this->rawToInventory($inventoryRaw);

		return $this->cachedInventories[$inventory->getId()] = $inventory;
	}

	public function delete(PerpetuatedVirtualInventory $inventory) : void {
		try {
			$this->dao->begin();
			$this->dao->delete($inventory->getId());
			$this->dao->commit();
		} catch(DatabaseException $exception) {
			$this->dao->rollback();
			throw $exception;
		}
		$inventory->onDelete();
		unset($this->cachedInventories[$inventory->getId()]);
	}
}