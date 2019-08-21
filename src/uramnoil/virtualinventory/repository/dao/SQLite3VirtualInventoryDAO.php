<?php


namespace uramnoil\virtualinventory\repository\dao;


use Exception;
use SQLite3;
use uramnoil\virtualinventory\VirtualInventoryPlugin;
use const SQLITE3_OPEN_CREATE;

class SQLite3VirtualInventoryDAO implements VirtualInventoryDAO {
	public const INVENTORY_TYPE_NONE = -1;
	public const INVENTORY_TYPE_CHEST = 0;
	public const INVENTORY_TYPE_DOUBLE_CHEST = 1;

	/** @var VirtualInventoryPlugin */
	private $plugin;
	/** @var SQLite3*/
	private $db;

	public function __construct(VirtualInventoryPlugin $plugin) {
		$this->plugin = $plugin;
	}

	public function open() : void {
		try {
			$this->db = new SQLite3($this->plugin->getDataFolder() . "virtualinventory.db", SQLITE3_OPEN_CREATE);
			$stmt = $this->db->prepare(
			/** @lang SQLite */
				<<<SQL_CREATE_TABLE
				CREATE TABLE IF NOT EXISTS inventories(
					inventory_id    INTEGER PRIMARY KEY AUTOINCREMENT,
					inventory_type  INTEGER NOT NULL,
					owner_id INTEGER NOT NULL,
					FOREIGN KEY (owner_id) REFERENCES owners(owner_id),
					FOREIGN KEY (inventory_type) REFERENCES inventory_types(inventory_type) 
				);

				CREATE TABLE IF NOT EXISTS items(
					inventory_id INTEGER PRIMARY KEY,
					slot    	 INTEGER NOT NULL,
					item_id 	 INTEGER NOT NULL,
					count   	 INTEGER NOT NULL,
					damage  	 INTEGER NOT NULL,
					nbt_b64 	 BLOB,
					UNIQUE(inventory_id, slot),
					FOREIGN KEY (inventory_id) REFERENCES inventories(inventory_id)
				);

				CREATE TABLE IF NOT EXISTS owners(
					owner_id   INTEGER PRIMARY KEY AUTOINCREMENT,
					owner_name TEXT NOT NULL UNIQUE
				);
				
				CREATE TABLE IF NOT EXISTS inventory_types(
					inventory_type INTEGER PRIMARY KEY,
					inventory_type_name TEXT NOT NULL UNIQUE
				)
			SQL_CREATE_TABLE
			);
			$stmt->execute();
		} catch(Exception $exception) {
			throw new TransactionException($exception);
		}
	}

	public function close() : void {
		$this->db->close();
	}

	public function delete(int $id) : void {
		try {
			$this->begin();
			$stmt = $this->db->prepare(
				<<<SQL_DELETE
					DELETE FROM inventories WHERE id = :id;
					DELETE FROM items WHERE inventory_id = :id
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
				<<<SQL_FIND_BY_ID
					SELECT inventory_id, inventory_type, owner_name, json_items FROM inventories
						NATURAL LEFT OUTER JOIN owners
						    NATURAL LEFT OUTER JOIN
					   			(SELECT inventory_id, json_group_array(json_object(item_id, slot, count, damage, nbt_b64)) as json_items FROM items)
					WHERE inventory_id = :id
					GROUP BY inventory_id
				SQL_FIND_BY_ID
			);
			$stmt->bindValue(':id', $id);
			$result = $stmt->execute();
		} catch(Exception $exception) {
			throw new TransactionException($exception);
		}

		$inventory['owner'] = $result['owner'];
		$inventory['id'] = $result[''];

		foreach($result->fetchArray() as $item) {
		}

		return $items;
	}

	public function findByOwner(string $name) : array {
		$inventories = [];

		try {
			$stmt = $this->db->prepare(
				<<<SQL_FIND_BY_OWNER
				SELECT items.item_id, items.slot, items.count, items.damage, items.nbt_b64 FROM items
				WHERE items.inventory_id IN(
				    SELECT inventories.id FROM inventories
				    	INNER JOIN owners
				    	ON owners.id = inventories.owner
				    WHERE owners.name = :name
				)
			SQL_FIND_BY_OWNER
			);
			$stmt->bindValue(':name', $name);
			$result = $stmt->execute();
		} catch(Exception $exception) {
			throw new TransactionException($exception);
		}

		//TODO 配列の構成
		return $inventories;
	}

	public function update(int $id, array $items) : void {
		try {
			$this->begin();
			foreach($items as $slot => $item) {
				$stmt = $this->db->prepare(
				/** @lang SQLite */
					<<<SQL_UPDATE
						REPLACE INTO items(item_id, count, damage, nbt_b64) VALUES(:id, :count, :damage, :nbt_b64)
					SQL_UPDATE
				);
				$stmt->execute();
			}
			$this->commit();
		} catch(Exception $exception) {
			$this->rollback();
			throw new TransactionException($exception);
		}
	}

	private function begin() : void {
		$this->db->exec(
		/** @lang SQLite */
			<<<SQL_BEGIN
				BEGIN
			SQL_BEGIN);
	}

	private function commit() : void {
		$this->db->exec(
		/** @lang SQLite */
			<<<SQL_COMMIT
				COMMIT
			SQL_COMMIT
		);
	}

	private function rollback() : void {
		$this->db->exec(
		/** @lang SQLite */
			<<<SQL_ROLLBACK
					ROLLBACK
				SQL_ROLLBACK
		);
	}
}