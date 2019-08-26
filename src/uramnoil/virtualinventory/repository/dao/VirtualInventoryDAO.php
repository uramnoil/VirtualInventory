<?php


namespace uramnoil\virtualinventory\repository\dao;


use uramnoil\virtualinventory\repository\InventoryIds;
use uramnoil\virtualinventory\VirtualInventoryPlugin;

interface VirtualInventoryDAO extends DAO {
	public const OPTION_IDS_NOT_IN = 0;

	/**
	 * @param string $ownerName
	 * @param int    $type
	 *
	 * @return array
	 */
	public function create(string $ownerName, int $type) : array;

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	public function findById(int $id) : array;

	/**
	 * @param string $name
	 * @param array  $option
	 *
	 * @return array
	 */
	public function findByOwner(string $name, array $option = []) : array;

	/**
	 * @param int $id
	 */
	public function delete(int $id) : void;

	/**
	 * @param int   $id
	 * @param array $items
	 */
	public function update(int $id, array $items) : void;
}