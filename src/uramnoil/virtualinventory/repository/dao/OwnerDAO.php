<?php


namespace uramnoil\virtualinventory\repository\dao;

interface OwnerDAO extends DAO {
	/**
	 * @param string $name
	 */
	public function create(string $name) : void;

	/**
	 * @param string $name
	 */
	public function delete(string $name) : void;

	/**
	 * @param string $name
	 */
	public function exists(string $name) : void;
}