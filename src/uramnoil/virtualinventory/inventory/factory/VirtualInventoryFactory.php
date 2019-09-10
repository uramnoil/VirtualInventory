<?php


namespace uramnoil\virtualinventory\inventory\factory;


use pocketmine\IPlayer;
use uramnoil\virtualinventory\inventory\VirtualInventory;
use uramnoil\virtualinventory\repository\VirtualInventoryRepository;

interface VirtualInventoryFactory {
	public function __construct(VirtualInventoryRepository $repository);

	public function createFrom(int $id, IPlayer $owner, string $title) : VirtualInventory;
}