<?php

namespace uramnoil\virtualchest\repository;

use pocketmine\IPlayer;
use uramnoil\virtualchest\inventory\VirtualChestInventory;

interface VirtualChestInventoryRepository {
	/**
	 * IDでVirtualChestInventoryを探します.
	 *
	 * @param int $id
	 *
	 * @return VirtualChestInventory
	 */
	public function findById(int $id) : VirtualChestInventory;

	/**
	 * 所有者でVirtualChestInventoryを探します.
	 *
	 * @param IPlayer $owner
	 *
	 * @return VirtualChestInventory[]
	 */
	public function findByOwner(IPlayer $owner) : array;

	/**
	 * VirtualChestInventoryをリポジトリから削除します.
	 *
	 * @param VirtualChestInventory $inventory
	 */
	public function delete(VirtualChestInventory $inventory) : void;

	/**
	 * 新しいVirtualChestInventoryを作成します.
	 *
	 * @param IPlayer $owner
	 *
	 * @return VirtualChestInventory
	 */
	public function new(IPlayer $owner) : VirtualChestInventory;

	/**
	 * VirtualChestInvnentoryをセーブします.
	 *
	 * @param \uramnoil\virtualchest\inventory\VirtualChestInventory $inventory
	 */
	public function save(VirtualChestInventory $inventory) : void;
}