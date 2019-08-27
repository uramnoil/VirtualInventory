<?php


namespace uramnoil\virtualinventory\repository\sqlite\dao;


use Exception;
use pocketmine\plugin\PluginBase;
use SQLite3;
use uramnoil\virtualinventory\repository\dao\OwnerDAO;
use uramnoil\virtualinventory\repository\DatabaseException;
use function strtolower;
use const SQLITE3_OPEN_CREATE;

class SQLiteOwnerDao implements OwnerDAO {
	/** @var PluginBase  */
	private $plugin;
	/** @var SQLite3 */
	private $db;

	public function __construct(PluginBase $plugin) {
		$this->plugin = $plugin;
	}

	public function open() : void {
		try {
			$this->db = new SQLite3($this->plugin->getDataFolder() . 'inventory.db', SQLITE3_OPEN_CREATE);
			$this->db->busyTimeout(1000);
			$this->db->exec(
				<<<SQL
				CREATE TABLE IF NOT EXISTS owners(
					owner_id   INTEGER PRIMARY KEY AUTOINCREMENT,
					owner_name TEXT NOT NULL UNIQUE
				);
				SQL);
		} catch(Exception $exception) {
			throw new DatabaseException($exception);
		}
	}

	public function close() : void {
		$this->db->close();
	}

	public function create(string $name) : void {
		$stmt = $this->db->prepare(
			<<<SQL
			INSERT INTO owners (owner_name) VALUES(:name)
			SQL);
		$stmt->bindValue(':name', strtolower($name));
		$stmt->execute();
	}

	public function delete(string $name) : void {
		$stmt = $this->db->prepare(
			<<<SQL
			DELETE FROM owners WHERE owner_name = :owner_name;
			SQL
		);
		$stmt->bindValue(':owner_name', strtolower($name));
		$stmt->execute();
	}

	public function exists(string $name) : bool {
		$stmt = $this->db->prepare(
			<<<SQL
			SELECT COUNT(*) AS count FROM owners WHERE owner_name = :name
			SQL
		);
		$stmt->bindValue(':name', strtolower($name));
		$result = $stmt->execute();
		$count = $result->fetchArray();
		assert($count['count'] = 1);
		return $result === 1;
	}
}