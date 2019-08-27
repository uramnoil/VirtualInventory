<?php


namespace uramnoil\virtualinventory\repository\sqlite\dao;


use Exception;
use pocketmine\plugin\PluginBase;
use SQLite3;
use uramnoil\virtualinventory\repository\dao\VirtualInventoryDAO;
use uramnoil\virtualinventory\repository\TransactionException;
use const SQLITE3_OPEN_CREATE;

class SQLiteVirtualInventoryDAO implements VirtualInventoryDAO {
	/** @var PluginBase */
	private $plugin;
	/** @var SQLite3*/
	private $db;

	public function __construct(PluginBase $plugin) {
		$this->plugin = $plugin;
	}

	public function open() : void {
		try {
			$this->db = new SQLite3($this->plugin->getDataFolder() . "inventory.db", SQLITE3_OPEN_CREATE);
			$this->db->busyTimeout(1000);
			$this->db->exec(
			/** @lang SQLite */
				//PHP7.3 ヒアドキュメント
				<<<SQL_CREATE_TABLE
				CREATE TABLE IF NOT EXISTS inventories(
					inventory_id    INTEGER PRIMARY KEY AUTOINCREMENT,
					inventory_type  INTEGER NOT NULL,
					owner_id INTEGER NOT NULL,
					FOREIGN KEY (owner_id) REFERENCES owners(owner_id) ON DELETE CASCADE,
					FOREIGN KEY (inventory_type) REFERENCES inventory_types(inventory_type)
				);
				
				CREATE TABLE IF NOT EXISTS items(
					inventory_id INTEGER PRIMARY KEY,
					slot    	 INTEGER NOT NULL,
					id 	 		 INTEGER NOT NULL,
					count   	 INTEGER NOT NULL,
					damage  	 INTEGER NOT NULL,
					nbt_b64 	 BLOB,
					UNIQUE(inventory_id, slot),
					FOREIGN KEY (inventory_id) REFERENCES inventories(inventory_id) ON DELETE CASCADE
				);
				
				CREATE TABLE IF NOT EXISTS inventory_types(
					inventory_type INTEGER PRIMARY KEY,
					inventory_type_name TEXT NOT NULL UNIQUE
				)
				SQL_CREATE_TABLE
			);
		} catch(Exception $exception) {
			throw new TransactionException($exception);
		}
	}

	public function close() : void {
		$this->db->close();
	}

	public function create(string $ownerName, int $type) : array {
		try {
			$this->begin();
			$stmt = $this->db->prepare(
				/** @lang SQLite */
				<<<SQL_CREATE
				INSERT INTO inventories(inventory_type, owner_id)
				SELECT :type, owner_id FROM owners
				WHERE owner_name = :owner_name
				SQL_CREATE
			);
			$stmt->bindValue(':owner_name', strtolower($ownerName));
			$result = $stmt->execute();
		} catch(Exception $exception) {
			throw new TransactionException($exception);
		}

		return $result->fetchArray();
	}

	public function delete(int $id) : void {
		try {
			$this->begin();
			$stmt = $this->db->prepare(
				/** @SQLite */
				<<<SQL_DELETE
				DELETE FROM inventories WHERE inventory_id = :id;
				SQL_DELETE
			);
			$stmt->bindValue(':id', $id);
			$stmt->execute();
			$this->commit();
		} catch(Exception $exception) {
			$this->rollback();
			throw new TransactionException($exception);
		}
	}

	public function findById(int $id) : array {
		$inventory = [];

		try {
			$stmt = $this->db->prepare(
				/** @lang SQLite */
				<<<SQL_FIND_BY_ID
				SELECT * FROM inventories 
					NATURAL INNER JOIN owners
				WHERE inventory_id = :id
				SQL_FIND_BY_ID
			);
			$stmt->bindValue(':id', $id);
			$inventoryResult = $stmt->execute();
		} catch(Exception $exception) {
			throw new TransactionException($exception);
		}

		$inventoryRaw = $inventoryResult->fetchArray();

		try {
			$stmt = $this->db->prepare(
			/** @lang SQLite */
				<<<SQL_FIND_BY_ID
				SELECT * FROM items
				WHERE inventory_id = :id
				SQL_FIND_BY_ID
			);
			$stmt->bindValue(':id', $inventoryRaw['inventory_id']);
			$itemsResult = $stmt->execute();
		} catch(Exception $exception) {
			throw new TransactionException($exception);
		}

		while($itemRaw = $itemsResult->fetchArray()) {
			$inventoryRaw['items'][$itemRaw['slot']] = $itemRaw;
		}

		return $inventory;
	}

	public function findByOwner(string $name, array $option = []) : array {
		try {
			$stmt = $this->db->prepare(
				/** @lang SQLite */
				<<<SQL_FIND_BY_OWNER
				SELECT * FROM inventories
					NATURAL INNER JOIN owners
				WHERE owner_name = :name
				SQL_FIND_BY_OWNER
			);
			$stmt->bindValue(':name', strtolower($name));
			$inventoryResult = $stmt->execute();
		} catch(Exception $exception) {
			throw new TransactionException($exception);
		}

		$inventoryRaws = [];
		$inventoryIds = [];

		while($inventoryRaw = $inventoryResult->fetchArray()) {
			$inventoryRaws[] = $inventoryRaw;
			$inventoryIds[] = $inventoryRaw['inventory_id'];
		}

		try {
			$stmt = $this->db->prepare(
			/** @lang SQLite */
			<<<SQL_FIND_BY_ID
			SELECT * FROM items
			WHERE inventory_id IN :array
			SQL_FIND_BY_ID
			);
			$stmt->bindValue(':array', implode(', ', $inventoryIds));
			$itemsResult = $stmt->execute();
		} catch(Exception $exception) {
			throw new TransactionException($exception);
		}

		while($itemRaw = $itemsResult->fetchArray()) {
			foreach($inventoryRaws as &$inventoryRaw) {
				if($inventoryRaw['inventory_id'] === $itemRaw['inventory_id']) {
					$inventoryRaw['items'][$itemRaw['slot']] = $itemRaw;
					break;
				}
			}
		}

		return $inventoryRaws;
	}

	public function update(array $inventory) : void {
		try {
			$this->begin();
			foreach($inventory['items'] as $slot => $item) {		// OPTIMIZE: ループ中のクエリ発行をどうにかする
				$stmt = $this->db->prepare(
				/** @lang SQLite */
					<<<SQL_UPDATE
					UPDATE items SET item_id = :id, count = :count, damage = :damage, nbt_b64 = :nbt_b64
					WHERE inventory_id = :inventory_id
					SQL_UPDATE
				);
				$stmt->bindValue('id', $item['id']);
				$stmt->bindValue('count', $item['count']);
				$stmt->bindValue('damage', $item['damage']);
				$stmt->bindValue('nbt_b64', $item['nbt_64']);
				$stmt->bindValue('inventory_id', $inventory['inventory_id']);
				$stmt->execute();
			}
			$this->commit();
		} catch(Exception $exception) {
			$this->rollback();
			throw new TransactionException($exception);
		}
	}

	/**
	 * トランザクションを開始します.
	 */
	private function begin() : void {
		$this->db->exec(
			/** @lang SQLite */
			<<<SQL_BEGIN
			BEGIN
			SQL_BEGIN);
	}

	/**
	 * トランザクションをコミットさせます.
	 */
	private function commit() : void {
		$this->db->exec(
			/** @lang SQLite */
			<<<SQL_COMMIT
			COMMIT
			SQL_COMMIT
		);
	}

	/**
	 * トランザクションをロールバックします.
	 */
	private function rollback() : void {
		$this->db->exec(
			/** @lang SQLite */
			<<<SQL_ROLLBACK
			ROLLBACK
			SQL_ROLLBACK
		);
	}
}