<?php


namespace uramnoil\virtualinventory\inventory\factory;


use pocketmine\IPlayer;
use uramnoil\virtualinventory\inventory\PerpetuatedVirtualInventory;

interface PerpetuatedVirtualInventoryFactory {
	public function createFrom(int $id, IPlayer $owner) : PerpetuatedVirtualInventory;
}