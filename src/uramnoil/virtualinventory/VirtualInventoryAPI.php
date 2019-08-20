<?php

namespace uramnoil\virtualinventory;

use pocketmine\IPlayer;
use uramnoil\virtualinventory\inventory\VirtualInventory;

interface VirtualInventoryAPI {
	/**
	 * IDからVirtualChestInventory探します.
	 *
	 * @param int $id
	 *
	 * @return VirtualInventory
	 */
	public function findById(int $id) : VirtualInventory;

	/**
	 * プレイヤーが所有しているVirtualChestInventoryを配列で返します.
	 *
	 * @param IPlayer $owner
	 *
	 * @return VirtualInventory[]
	 */
	public function findByOwner(IPlayer $owner) : array;

	/**
	 * VirtualChestInventoryを削除します.
	 *
	 * @param \uramnoil\virtualinventory\inventory\VirtualInventory $inventory
	 */
	public function delete(VirtualInventory $inventory) : void;

	/**
	 * @param \pocketmine\IPlayer $owner
	 *
	 * @return \uramnoil\virtualinventory\inventory\VirtualInventory
	 */
	public function new(IPlayer $owner) : VirtualInventory;
}