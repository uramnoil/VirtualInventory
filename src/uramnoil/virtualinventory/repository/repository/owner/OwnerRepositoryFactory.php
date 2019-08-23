<?php


namespace uramnoil\virtualinventory\repository\sqlite\repository\virtualinventory\dao\owner;


use pocketmine\plugin\PluginBase;
use SQLite3;
use uramnoil\virtualinventory\repository\sqlite\repository\owner\SQLiteOwnerRepository;

class OwnerRepositoryFactory {
	/** @var PluginBase */
	private $plugin;

	/**
	 * @param PluginBase $plugin
	 */
	public function __construct(PluginBase $plugin) {
		$this->plugin = $plugin;
	}

	/**
	 * @return OwnerRepository
	 */
	public function create() : OwnerRepository {
		return new SQLiteOwnerRepository($this->plugin);
	}
}