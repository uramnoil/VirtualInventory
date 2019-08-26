<?php


namespace uramnoil\virtualinventory\inventory\factory;


use pocketmine\IPlayer;
use uramnoil\virtualinventory\inventory\VirtualChestInventory;
use uramnoil\virtualinventory\inventory\VirtualInventory;
use uramnoil\virtualinventory\repository\VirtualInventoryRepository;

class VirtualChestInventoryFactory implements VirtualInventoryFactory {
	private $repository;

	public function __construct(VirtualInventoryRepository $repository) {
		$this->repository = $repository;
	}

	public function createFrom(int $id, IPlayer $owner) : VirtualInventory {
		return new VirtualChestInventory($this->repository, $id, $owner);
	}
}