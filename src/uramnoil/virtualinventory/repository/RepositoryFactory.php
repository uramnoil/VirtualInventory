<?php


namespace uramnoil\virtualinventory\repository;


use SQLite3;
use uramnoil\virtualinventory\repository\sqlite\SQLiteOwnerRepository;
use uramnoil\virtualinventory\repository\sqlite\SQLiteVirtualInventoryRepository;
use uramnoil\virtualinventory\VirtualInventoryPlugin;

class RepositoryFactory {
	/** @var SQLite3 */
	private $db;

	public function __construct(VirtualInventoryPlugin $plugin) {
		$this->db = new SQLite3($plugin->getDataFolder() . '/virtualinventory.db');
	}

	public function createVirtualInventoryRepository() : VirtualInventoryRepository {
		return new SQLiteVirtualInventoryRepository($this->db);
	}

	/**
	 * @return OwnerRepository
	 */
	public function createOwnerRepository() : OwnerRepository {
		return new SQLiteOwnerRepository($this->db);
	}

}