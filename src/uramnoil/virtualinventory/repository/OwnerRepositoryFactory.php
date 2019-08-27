<?php


namespace uramnoil\virtualinventory\repository;


use pocketmine\plugin\PluginBase;
use uramnoil\virtualinventory\repository\sqlite\SQLiteOwnerRepository;

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