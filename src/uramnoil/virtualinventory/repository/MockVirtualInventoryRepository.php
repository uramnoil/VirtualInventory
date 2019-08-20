<?php


namespace uramnoil\virtualinventory\repository;


use pocketmine\IPlayer;
use uramnoil\virtualinventory\inventory\VirtualInventory;

class MockVirtualInventoryRepository implements VirtualInventoryRepository {
	public function delete(VirtualInventory $inventory) : void {
		// TODO: Implement delete() method.
	}

	public function findById(int $id) : VirtualInventory {
		// TODO: Implement findById() method.
	}

	public function findByOwner(IPlayer $owner) : array {
		// TODO: Implement findByOwner() method.
	}

	public function new(IPlayer $owner) : VirtualInventory {
		// TODO: Implement new() method.
	}

	public function save(VirtualInventory $inventory) : void {
		// TODO: Implement save() method.
	}
}