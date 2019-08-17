<?php


namespace uramnoil\virtualchest\inventory;


use pocketmine\IPlayer;

interface VirtualChestInventoryFactory {
	public function createFrom(int $id, IPlayer $owner) : VirtualChestInventory;
}