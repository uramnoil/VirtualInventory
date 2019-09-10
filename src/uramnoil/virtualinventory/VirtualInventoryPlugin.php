<?php


namespace uramnoil\virtualinventory;


use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Utils;
use uramnoil\virtualinventory\extension\SchedulerTrait;
use uramnoil\virtualinventory\inventory\PerpetuatedVirtualInventory;
use uramnoil\virtualinventory\listener\RegisterOwnerListener;
use uramnoil\virtualinventory\repository\OwnerRepository;
use uramnoil\virtualinventory\repository\RepositoryFactory;
use uramnoil\virtualinventory\repository\VirtualInventoryRepository;
use uramnoil\virtualinventory\task\TransactionTask;
use uramnoil\virtualinventory\task\TransactionWithResultTask;

class VirtualInventoryPlugin extends PluginBase implements VirtualInventoryAPI {
	use SchedulerTrait;

	/** @var VirtualInventoryAPI */
	private $api;
	/** @var RepositoryFactory */
	private $factory;
	/** @var OwnerRepository */
	private $ownerRepository;
	/** @var VirtualInventoryRepository */
	private $inventoryRepository;

	public function onLoad() {
		$this->api = $this;
		$factory = new RepositoryFactory($this);
		$this->ownerRepository = $factory->createVirtualInventoryRepository();
		$this->inventoryRepository = $factory->createOwnerRepository();
	}

	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents(new RegisterOwnerListener($this), $this);
	}

	public function onDisable() {
	    $this->factory->close();
	}

	public function getAPI() : VirtualInventoryAPI {
		return $this->api;
	}

	public function findById(int $id, callable $onDone) : void {
		if($this->isDisabled()) {
			throw new VirtualInventoryException('VirtualInventoryPlugin is disabled.');
		}

		Utils::validateCallableSignature(function(?PerpetuatedVirtualInventory $inventory) : void{}, $onDone);

		$task = new TransactionWithResultTask(function() use($id) : ?PerpetuatedVirtualInventory {
			return $this->inventoryRepository->findById($id);
		}, $onDone);

		$this->submitTask($task);
	}

	public function findByOwner(IPlayer $owner, callable $onDone) : void {
		if($this->isDisabled()) {
			throw new VirtualInventoryException('VirtualInventoryPlugin is disabled.');
		}

		Utils::validateCallableSignature(function(array $inventories) : void{}, $onDone);

		$task = new TransactionWithResultTask(function() use($owner) : array {
			return $this->inventoryRepository->findByOwner($owner);
		}, $onDone);

		$this->submitTask($task);
	}

	public function delete(PerpetuatedVirtualInventory $inventory, ?callable $onDone) : void {
		if($this->isDisabled()) {
			throw new VirtualInventoryException('VirtualInventoryPlugin is disabled.');
		}

		isset($onDone) ?
			Utils::validateCallableSignature(function(?object $result) : void{}, $onDone)
			: $onDone = function(?object $result) : void {};

		$task = new TransactionTask(function() use($inventory) : void {
			if($this->isDisabled()) {
				throw new VirtualInventoryException('VirtualInventoryPlugin is disabled.');
			}
			$this->inventoryRepository->delete($inventory);
		}, $onDone);

		$this->submitTask($task);
	}

	public function new(IPlayer $owner, int $type, string $title, callable $onDone) : void {
		if($this->isDisabled()) {
			throw new VirtualInventoryException('VirtualInventoryPlugin is disabled.');
		}

		$task = new TransactionTask(function() use ($owner, $type, $title) : PerpetuatedVirtualInventory {
			return $this->inventoryRepository->new($owner, $type, $title);
		}, $onDone ?? function(PerpetuatedVirtualInventory $inventory) : void {});

		$this->submitTask($task);
	}

	public function save(PerpetuatedVirtualInventory $inventory, callable $onDone) : void {
		if($this->isDisabled()) {
			throw new VirtualInventoryException('VirtualInventoryPlugin is disabled.');
		}

		$onDone !== null ?
			Utils::validateCallableSignature(function() : void{}, $onDone)
			: $onDone = function(object $onUse) : void {};

		$task = new TransactionTask(function() use ($inventory) : void {
			$this->inventoryRepository->save($inventory);
		}, $onDone);

		$this->submitTask($task);
	}

	public function registerOwner(IPlayer $owner, ?callable $onDone) : void {
		if($this->isDisabled()) {
			throw new VirtualInventoryException('VirtualInventoryPlugin is disabled.');
		}

		if($this->isDisabled()) {
			throw new VirtualInventoryException('VirtualInventoryPlugin is disabled.');
		}
		$this->ownerRepository->new($owner, $onDone);
	}

	public function unregisterOwner(IPlayer $owner, ?callable $onDone) : void {
		if($this->isDisabled()) {
			throw new VirtualInventoryException('VirtualInventoryPlugin is disabled.');
		}

		if($this->isDisabled()) {
			throw new VirtualInventoryException('VirtualInventoryPlugin is disabled.');
		}
		$this->ownerRepository->delete($owner, $onDone);
	}
}