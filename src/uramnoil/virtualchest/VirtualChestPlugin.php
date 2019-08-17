<?php


namespace uramnoil\virtualchest;


use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use uramnoil\virtualchest\inventory\VirtualChestInventory;
use uramnoil\virtualchest\repository\VirtualChestInventoryRepository;

class VirtualChestPlugin extends PluginBase implements VirtualChestAPI {
	/** @var VirtualChestAPI */
	private $api;
	/** @var VirtualChestInventoryRepository */
	private $repository;

	public function onLoad() {
		$this->api = $this;
	}

	public function onDisable() {
		$this->repository->save();
	}

	public function getAPI() : VirtualChestAPI {
		return $this->api;
	}

	public function findById(int $id) : VirtualChestInventory {
		return $this->repository->findById($id);
	}

	public function findByOwner(IPlayer $owner) : array {
		return $this->repository->findByOwner($owner);
	}

	public function delete(VirtualChestInventory $inventory) : void {
		$this->repository->delete($inventory);
	}

	public function new($owner) : VirtualChestInventory {
		return $this->repository->new($owner);
	}

	public function save(VirtualChestInventory $inventory) : void {
		$this->repository->save($inventory);
	}
}