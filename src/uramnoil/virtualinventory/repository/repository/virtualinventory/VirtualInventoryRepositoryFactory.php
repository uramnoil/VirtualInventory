<?php


namespace uramnoil\virtualinventory\repository\sqlite\repository\virtualinventory;


use uramnoil\virtualinventory\VirtualInventoryPlugin;

class VirtualInventoryRepositoryFactory {
	/** @var \uramnoil\virtualinventory\VirtualInventoryPlugin  */
	private $plugin;

	public function __construct(VirtualInventoryPlugin $plugin) {
		$this->plugin = $plugin;
	}

	public function create() : VirtualInventoryRepository {
		return new SQLiteVirtualInventoryRepository($this->plugin);
	}
}