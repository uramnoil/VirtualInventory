<?php

namespace uramnoil\virtualinventory;

use pocketmine\IPlayer;
use uramnoil\virtualinventory\inventory\VirtualInventory;

interface VirtualInventoryAPI {
	/**
	 * IDからVirtualChestInventory探します.
	 *
	 * @param int      $id
	 * @param callable $onDone
	 */
	public function findById(int $id, callable $onDone) : void;

	/**
	 * プレイヤーが所有しているVirtualChestInventoryを配列で返します.
	 *
	 * @param IPlayer  $owner
	 * @param callable $onDone
	 */
	public function findByOwner(IPlayer $owner, callable $onDone) : void;

	/**
	 * VirtualChestInventoryを削除します.
	 *
	 * @param VirtualInventory $inventory
	 * @param callable|null    $onDone
	 */
	public function delete(VirtualInventory $inventory, ?callable $onDone) : void;

	/**
	 * @param IPlayer  $owner
	 * @param int      $type
	 * @param callable $onDone
	 *
	 */
	public function newInventory(IPlayer $owner, int $type, callable $onDone) : void;

	/**
	 * @param IPlayer $owner
	 */
	public function registerOwner(IPlayer $owner) : void;

	/**
	 * @param IPlayer $owner
	 */
	public function unregisterOwner(IPlayer $owner) : void;
}