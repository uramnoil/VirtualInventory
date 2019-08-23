<?php


namespace uramnoil\virtualinventory\repository\sqlite\dao;


use Exception;
use pocketmine\plugin\PluginBase;
use SQLite3;
use uramnoil\virtualinventory\repository\dao\owner\OwnerDAO;
use uramnoil\virtualinventory\repository\dao\virtualinventory\TransactionException;

class SQLiteOwnerDao implements OwnerDAO {
	/** @var PluginBase  */
	private $plugin;
	private $db;

	public function __construct(PluginBase $plugin) {
		$this->plugin = $plugin;
	}

	public function open() : void {
		try {
			$this->db = new SQLite3($this->plugin->getDataFolder() . 'virtualinventory.db');
			$this->db->exec(
			/** @lang SQLite */
			//PHP7.3 ヒアドキュメント
				<<<SQL_CREATE_TABLE
			CREATE TABLE IF NOT EXISTS owners(
				owner_id   INTEGER PRIMARY KEY AUTOINCREMENT,
				owner_name TEXT NOT NULL UNIQUE
			);
			SQL_CREATE_TABLE);
		} catch(Exception $exception) {
			throw new TransactionException($exception);
		}

	}

	public function close() : void {
		$this->db->close;
	}

	public function create(string $name) : void {
		// TODO: Implement create() method.
	}

	public function delete(string $name) : void {
		// TODO: Implement delete() method.
	}
}