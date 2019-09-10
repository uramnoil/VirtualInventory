<?php

namespace uramnoil\virtualinventory\repository;

use pocketmine\IPlayer;
use uramnoil\virtualinventory\inventory\PerpetuatedVirtualInventory;

interface VirtualInventoryRepository extends Repository {
	/**
	 * IDでVirtualChestInventoryを探します.
	 *
	 * @param  int  $id
	 *
	 * @return PerpetuatedVirtualInventory
	 */
	public function findById(int $id) : PerpetuatedVirtualInventory;

	/**
	 * 所有者でVirtualChestInventoryを探します.
	 *
	 * @param  IPlayer  $owner
	 *
	 * @return array
	 */
	public function findByOwner(IPlayer $owner) : array;

	/**
	 * VirtualChestInventoryをリポジトリから削除します.
	 *
	 * @param  PerpetuatedVirtualInventory  $inventory
	 */
	public function delete(PerpetuatedVirtualInventory $inventory) : void;

	/**
	 * 新しいVirtualChestInventoryを作成します.
	 *
	 * @param  IPlayer  $owner
	 * @param  int  $inventoryType
	 * @param  string  $title
	 *
	 * @return PerpetuatedVirtualInventory
	 */
	public function new(IPlayer $owner, int $inventoryType, string $title) : PerpetuatedVirtualInventory;

	/**
	 * VirtualChestInventoryをセーブします.
	 *
	 * @param  PerpetuatedVirtualInventory  $inventory
	 */
	public function save(PerpetuatedVirtualInventory $inventory) : void;
}