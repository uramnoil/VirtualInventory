<?php


namespace uramnoil\virtualinventory\inventory\factory;


use pocketmine\IPlayer;
use uramnoil\virtualinventory\inventory\PerpetuatedVirtualDoubleChestInventory;
use uramnoil\virtualinventory\inventory\VirtualInventory;
use uramnoil\virtualinventory\repository\VirtualInventoryRepository;

class VirtualDoubleChestInventoryFactory implements VirtualInventoryFactory {
	private $repository;

	public function __construct(VirtualInventoryRepository $repository) {
		$this->repository = $repository;
	}

	public function createFrom(int $id, IPlayer $owner) : VirtualInventory {
		return new PerpetuatedVirtualDoubleChestInventory($this->repository, $id, $owner);
	}
}