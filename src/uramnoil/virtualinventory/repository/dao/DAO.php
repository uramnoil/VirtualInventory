<?php


namespace uramnoil\virtualinventory\repository\dao\virtualinventory;


use uramnoil\virtualinventory\VirtualInventoryPlugin;

interface DAO {
	public function __construct(VirtualInventoryPlugin $plugin);
}