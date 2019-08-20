<?php


namespace uramnoil\virtualinventory\repository\dao;


interface VirtualInventoryDAO {
	/**
	 * @param int $id
	 *
	 * @return array
	 */
	public function findById(int $id) : array;

	/**
	 * @param string $name
	 *
	 * @return array
	 */
	public function findByOwner(string $name) : array;

	/**
	 * @param int $id
	 */
	public function delete(int $id) : void;

	/**
	 * @param int   $id
	 * @param array $inventory
	 */
	public function update(int $id, array $inventory) : void;

	/**
	 *
	 */
	public function close() : void;
}