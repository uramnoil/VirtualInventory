<?php


namespace uramnoil\virtualinventory\repository;


use pocketmine\plugin\PluginBase;
use SQLite3;

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