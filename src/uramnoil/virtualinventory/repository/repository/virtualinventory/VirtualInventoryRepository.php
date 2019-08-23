<?php

namespace uramnoil\virtualinventory\repository\sqlite\repository\virtualinventory;

use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use uramnoil\virtualinventory\inventory\VirtualInventory;

interface VirtualInventoryRepository extends Repository {
	/**
	 * IDでVirtualChestInventoryを探します.
	 *
	 * @param int 	  $id
	 *
	 * @return VirtualInventory
	 */
	public function findById(int $id) : VirtualInventory;

	/**
	 * 所有者でVirtualChestInventoryを探します.
	 *
	 * @param IPlayer $owner
	 *
	 * @return array
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
	 * @param int $inventoryType
	 *
	 * @return VirtualInventory
	 */
	public function new(IPlayer $owner, int $inventoryType) : VirtualInventory;

	/**
	 * VirtualChestInventoryをセーブします.
	 *
	 * @param VirtualInventory $inventory
	 */
	public function save(VirtualInventory $inventory) : void;
}