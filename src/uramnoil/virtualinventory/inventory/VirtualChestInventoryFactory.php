<?php


namespace uramnoil\virtualinventory\inventory;


use pocketmine\IPlayer;

interface VirtualChestInventoryFactory {
	public function createFrom(int $id, IPlayer $owner) : VirtualInventory;
}