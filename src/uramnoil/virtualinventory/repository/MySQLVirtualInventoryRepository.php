<?php


namespace uramnoil\virtualinventory\repository;


use Closure;
use pocketmine\IPlayer;
use uramnoil\virtualinventory\inventory\VirtualInventory;
use uramnoil\virtualinventory\VirtualInventoryPlugin;

class MySQLVirtualInventoryRepository implements VirtualInventoryRepository {
	/** @var VirtualInventoryPlugin  */
	private $plugin;
	private $dao;

	public function __construct(VirtualInventoryPlugin $plugin) {
		$this->plugin = $plugin;
	}

	public function save(VirtualInventory $inventory) : void {
		// TODO: Implement save() method.
	}

	public function new(IPlayer $owner, Closure $done) : VirtualInventory {
		// TODO: Implement new() method.
	}

	public function findByOwner(IPlayer $owner, Closure $done) : void {
		// TODO: Implement findByOwner() method.
	}

	public function findById(int $id, Closure $done) : VirtualInventory {
		// TODO: Implement findById() method.
	}

	public function delete(VirtualInventory $inventory) : void {
		// TODO: Implement delete() method.
	}

	public function close() : void {
		// TODO: Implement close() method.
	}
}