<?php


namespace uramnoil\virtualinventory\repository\dao\owner;


use uramnoil\virtualinventory\repository\dao\virtualinventory\DAO;

interface OwnerDAO extends DAO {
	public function create(string $name) : void;
	public function delete(string $name) : void;
}