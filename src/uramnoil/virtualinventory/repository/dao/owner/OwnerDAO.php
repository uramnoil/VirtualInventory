<?php


namespace uramnoil\virtualinventory\repository\dao\owner;


use uramnoil\virtualinventory\repository\dao\virtualinventory\DAO;

interface OwnerDAO extends DAO {

	/**
	 * @param string $name
	 */
	public function create(string $name) : void;

	/**
	 * @param string $name
	 */
	public function delete(string $name) : void;
}