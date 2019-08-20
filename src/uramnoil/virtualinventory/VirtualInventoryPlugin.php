<?php


namespace uramnoil\virtualinventory;


use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use uramnoil\virtualinventory\inventory\VirtualInventory;
use uramnoil\virtualinventory\repository\VirtualInventoryRepository;
use uramnoil\virtualinventory\repository\VirtualInventoryRepositoryFactory;

class VirtualInventoryPlugin extends PluginBase implements VirtualInventoryAPI {
	/** @var VirtualInventoryAPI */
	private $api;
	/** @var VirtualInventoryRepository */
	private $repository;

	public function onLoad() {
		$this->api = $this;
		$this->repository = (new VirtualInventoryRepositoryFactory())->create();
	}

	public function onDisable() {
		$this->repository->save();
	}

	public function getAPI() : VirtualInventoryAPI {
		return $this->api;
	}

	public function findById(int $id) : VirtualInventory {
		return $this->repository->findById($id);
	}

	public function findByOwner(IPlayer $owner) : array {
		return $this->repository->findByOwner($owner);
	}

	public function delete(VirtualInventory $inventory) : void {
		$this->repository->delete($inventory);
	}

	public function new($owner) : VirtualInventory {
		return $this->repository->new($owner);
	}

	public function save(VirtualInventory $inventory) : void {
		$this->repository->save($inventory);
	}
}