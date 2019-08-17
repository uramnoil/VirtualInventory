<?php


namespace uramnoil\virtualchest;

use pocketmine\IPlayer;
use uramnoil\virtualchest\inventory\VirtualChestInventory;

interface VirtualChestAPI {
	/**
	 * IDからVirtualChestInventory探します.
	 *
	 * @param int $id
	 *
	 * @return VirtualChestInventory
	 */
	public function findById(int $id) : VirtualChestInventory;

	/**
	 * プレイヤーが所有しているVirtualChestInventoryを配列で返します.
	 *
	 * @param IPlayer $owner
	 *
	 * @return VirtualChestInventory[]
	 */
	public function findByOwner(IPlayer $owner) : array;

	/**
	 * VirtualChestInventoryを削除します.
	 *
	 * @param \uramnoil\virtualchest\inventory\VirtualChestInventory $inventory
	 */
	public function delete(VirtualChestInventory $inventory) : void;

	/**
	 * @param \pocketmine\IPlayer $owner
	 *
	 * @return \uramnoil\virtualchest\inventory\VirtualChestInventory
	 */
	public function new(IPlayer $owner) : VirtualChestInventory;
}