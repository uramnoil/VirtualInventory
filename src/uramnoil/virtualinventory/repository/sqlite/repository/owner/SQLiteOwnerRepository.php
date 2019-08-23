<?php


namespace uramnoil\virtualinventory\repository\sqlite\repository\owner;

use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use uramnoil\virtualinventory\repository\sqlite\repository\virtualinventory\dao\owner\OwnerRepository;
use uramnoil\virtualinventory\repository\sqlite\repository\virtualinventory\sqlite\dao\SQLiteOwnerDao;

class SQLiteOwnerRepository implements OwnerRepository {
	/** @var PluginBase */
	private $plugin;
	private $db;

	public function __construct(PluginBase $plugin) {
		$this->plugin = $plugin;
		$this->db = new SQLiteOwnerDao($plugin);
	}

	public function open() : void {
		$this->db->open();
	}

	public function close() : void {
		$this->db->close();
	}

	public function new(IPlayer $player) : void {
		$this->db->create($player->getName());
	}

	public function delete(IPlayer $player) : void {
		$this->db->delete($player->getName());
	}
}