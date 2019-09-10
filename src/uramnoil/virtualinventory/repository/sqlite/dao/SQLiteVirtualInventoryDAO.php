<?php


namespace uramnoil\virtualinventory\repository\sqlite\dao;


use Exception;
use SQLite3;
use uramnoil\virtualinventory\repository\dao\Transactionable;
use uramnoil\virtualinventory\repository\dao\VirtualInventoryDAO;
use uramnoil\virtualinventory\repository\DatabaseException;
use function implode;
use function strtolower;

class SQLiteVirtualInventoryDAO implements VirtualInventoryDAO, Transactionable {
	/** @var SQLite3 */
	private $db;

	public function __construct(SQLite3 $db) {
		$this->db = $db;

		try {
			$this->db->exec(
			/** @lang SQLite */
			//PHP7.3 ヒアドキュメント
				<<<SQL
				CREATE TABLE IF NOT EXISTS inventories(
					inventory_id    INTEGER PRIMARY KEY AUTOINCREMENT,
					inventory_type  INTEGER NOT NULL,
					inventory_title TEXT,
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
				SQL
			);
		} catch (Exception $exception) {
			throw new DatabaseException($exception);
		}
	}

	public function create(string $ownerName, int $type, string $title) : array {
		try {
			$stmt = $this->db->prepare(
			/** @lang SQLite */
				<<<SQL
				INSERT INTO inventories(inventory_type, inventory_title ,owner_id)
				SELECT :type, :inventory_tityle, owner_id FROM owners
				WHERE owner_name = :owner_name
				SQL
			);
			$stmt->bindValue(':inventory_title', $title);
			$stmt->bindValue(':owner_name', strtolower($ownerName));
			$result = $stmt->execute();
		} catch (Exception $exception) {
			throw new DatabaseException($exception);
		}

		return $result->fetchArray();
	}

	public function delete(int $id) : void {
		try {
			$stmt = $this->db->prepare(
			/** @SQLite */
				<<<SQL
				DELETE FROM inventories WHERE inventory_id = :id;
				SQL
			);
			$stmt->bindValue(':id', $id);
			$stmt->execute();
		} catch (Exception $exception) {
			throw new DatabaseException($exception);
		}
	}

	public function findById(int $id) : array {
		$inventory = [];

		try {
			$stmt = $this->db->prepare(
			/** @lang SQLite */
				<<<SQL
				SELECT * FROM inventories 
					NATURAL INNER JOIN owners
				WHERE inventory_id = :id
				SQL
			);
			$stmt->bindValue(':id', $id);
			$inventoryResult = $stmt->execute();
		} catch (Exception $exception) {
			throw new DatabaseException($exception);
		}

		$inventoryRaw = $inventoryResult->fetchArray();

		try {
			$stmt = $this->db->prepare(
			/** @lang SQLite */
				<<<SQL
				SELECT * FROM items
				WHERE inventory_id = :id
				SQL
			);
			$stmt->bindValue(':id', $inventoryRaw['inventory_id']);
			$itemsResult = $stmt->execute();
		} catch (Exception $exception) {
			throw new DatabaseException($exception);
		}

		while ($itemRaw = $itemsResult->fetchArray()) {
			$inventoryRaw['items'][$itemRaw['slot']] = $itemRaw;
		}

		return $inventory;
	}

	public function findByOwner(string $name, array $option = []) : array {
		try {
			$stmt = null;
			if (empty($option[self::OPTION_IDS_NOT_IN])) {
				$stmt = $this->db->prepare(
				/** @lang SQLite */
					<<<SQL
					SELECT * FROM inventories
						NATURAL INNER JOIN owners
					WHERE owner_name = :name
					SQL
				);
			} else {
				$stmt = $this->db->prepare(
				/** @lang SQLite */
					<<<SQL
					SELECT * FROM inventories
						NATURAL INNER JOIN owners
					WHERE
						owner_name = :name AND
						inventory_id NOT IN(:ids)
					SQL
				);
				$stmt->bindValue(':ids', implode(',', $option[self::OPTION_IDS_NOT_IN]));
			}
			$stmt->bindValue(':name', strtolower($name));
			$inventoryResult = $stmt->execute();
		} catch (Exception $exception) {
			throw new DatabaseException($exception);
		}

		$inventoryRaws = [];
		$inventoryIds = [];

		while ($inventoryRaw = $inventoryResult->fetchArray()) {
			$inventoryRaws[] = $inventoryRaw;
			$inventoryIds[] = $inventoryRaw['inventory_id'];
		}

		try {
			$stmt = $this->db->prepare(
			/** @lang SQLite */
				<<<SQL
				SELECT * FROM items
				WHERE inventory_id IN :array
				SQL
			);
			$stmt->bindValue(':array', implode(', ', $inventoryIds));
			$itemsResult = $stmt->execute();
		} catch (Exception $exception) {
			throw new DatabaseException($exception);
		}

		while ($itemRaw = $itemsResult->fetchArray()) {
			foreach ($inventoryRaws as &$inventoryRaw) {
				if ($inventoryRaw['inventory_id'] === $itemRaw['inventory_id']) {
					$inventoryRaw['items'][$itemRaw['slot']] = $itemRaw;
					break;
				}
			}
		}

		return $inventoryRaws;
	}


	public function update(array $inventory) : void {
		try {
			foreach ($inventory['items'] as $slot => $item) {        // OPTIMIZE: ループ中のクエリ発行をどうにかする
				$stmt = $this->db->prepare(
				/** @lang SQLite */
					<<<SQL
					UPDATE items SET item_id = :id, count = :count, damage = :damage, nbt_b64 = :nbt_b64
					WHERE inventory_id = :inventory_id
					SQL
				);
				$stmt->bindValue('id', $item['id']);
				$stmt->bindValue('count', $item['count']);
				$stmt->bindValue('damage', $item['damage']);
				$stmt->bindValue('nbt_b64', $item['nbt_64']);
				$stmt->bindValue('inventory_id', $inventory['inventory_id']);
				$stmt->execute();
			}
		} catch (Exception $exception) {
			throw new DatabaseException($exception);
		}
	}

	public
	function begin() : void {
		$this->db->exec(
			<<<SQL
			BEGIN
			SQL
		);
	}

	public
	function commit() : void {
		$this->db->exec(
			<<<SQL
			COMMIT
			SQL
		);
	}

	public
	function rollback() : void {
		$this->db->exec(
			<<<SQL
			ROLLBACK
			SQL
		);
	}
}