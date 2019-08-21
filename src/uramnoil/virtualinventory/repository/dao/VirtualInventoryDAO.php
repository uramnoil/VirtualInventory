<?php


namespace uramnoil\virtualinventory\repository\dao;


use uramnoil\virtualinventory\VirtualInventoryPlugin;

interface VirtualInventoryDAO {
	public function __construct(VirtualInventoryPlugin $plugin);

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
	 * @param array $items
	 */
	public function update(int $id, array $items) : void;

	/**
	 * DBのコネクションを作ります.
	 */
	public function open() : void;

	/**
	 * DBのコネクションを終了します.
	 */
	public function close() : void;
}