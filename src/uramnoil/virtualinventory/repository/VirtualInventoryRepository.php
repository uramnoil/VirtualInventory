<?php

namespace uramnoil\virtualinventory\repository;

use pocketmine\IPlayer;
use uramnoil\virtualinventory\inventory\VirtualInventory;

interface VirtualInventoryRepository {
	/**
	 * IDでVirtualChestInventoryを探します.
	 *
	 * @param int $id
	 *
	 * @return VirtualInventory
	 */
	public function findById(int $id) : VirtualInventory;

	/**
	 * 所有者でVirtualChestInventoryを探します.
	 *
	 * @param IPlayer $owner
	 *
	 * @return VirtualInventory[]
	 */
	public function findByOwner(IPlayer $owner) : array;

	/**
	 * VirtualChestInventoryをリポジトリから削除します.
	 *
	 * @param VirtualInventory $inventory
	 */
	public function delete(VirtualInventory $inventory) : void;

	/**
	 * 新しいVirtualChestInventoryを作成します.
	 *
	 * @param IPlayer $owner
	 *
	 * @return VirtualInventory
	 */
	public function new(IPlayer $owner) : VirtualInventory;

	/**
	 * VirtualChestInvnentoryをセーブします.
	 *
	 * @param \uramnoil\virtualinventory\inventory\VirtualInventory $inventory
	 */
	public function save(VirtualInventory $inventory) : void;

	/**
	 * Repositoryを終了させます.
	 */
	public function close() : void;
}