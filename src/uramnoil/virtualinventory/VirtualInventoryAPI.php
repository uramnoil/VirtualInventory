<?php

namespace uramnoil\virtualinventory;

use pocketmine\IPlayer;
use uramnoil\virtualinventory\inventory\PerpetuatedVirtualInventory;

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
	 * @param PerpetuatedVirtualInventory $inventory
	 * @param callable|null    $onDone
	 */
	public function delete(PerpetuatedVirtualInventory $inventory, ?callable $onDone) : void;

	/**
	 * @param IPlayer  $owner
	 * @param int      $type
     * @param string   $title
	 * @param callable $onDone
	 *
	 */
	public function new(IPlayer $owner, int $type, string $title, callable $onDone) : void;

	/**
	 * プレイヤーをオーナーとして登録します.
	 * 基本的にVirtualInventoryPluginで管理しているので,外部からの操作はなるべく控えてください.
	 *
	 * @param IPlayer $owner
	 * @param callable|null $onDone
	 */
	public function registerOwner(IPlayer $owner, ?callable $onDone) : void;

	/**
	 * プレイヤーのオーナー登録を解除します.
	 * 基本的にVirtualInventoryPluginで管理しているので,外部からの操作はなるべく控えてください.
	 *
	 * @param IPlayer $owner
	 * @param callable|null $onDone
	 */
	public function unregisterOwner(IPlayer $owner, ?callable $onDone) : void;
}