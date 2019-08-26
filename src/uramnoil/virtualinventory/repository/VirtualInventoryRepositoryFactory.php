<?php


namespace uramnoil\virtualinventory\repository;


use uramnoil\virtualinventory\VirtualInventoryPlugin;

class VirtualInventoryRepositoryFactory {
	/** @var VirtualInventoryPlugin  */
	private $plugin;

	public function __construct(VirtualInventoryPlugin $plugin) {
		$this->plugin = $plugin;
	}

	public function create() : VirtualInventoryRepository {
		return new SQLiteVirtualInventoryRepository($this->plugin);
	}
}