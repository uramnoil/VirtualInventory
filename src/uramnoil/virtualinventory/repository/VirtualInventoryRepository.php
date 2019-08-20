<?php

namespace uramnoil\virtualinventory\repository;

use Closure;
use pocketmine\IPlayer;
use uramnoil\virtualinventory\inventory\VirtualInventory;
use uramnoil\virtualinventory\VirtualInventoryPlugin;

interface VirtualInventoryRepository {
	public function __construct(VirtualInventoryPlugin $plugin);

	/**
	 * IDでVirtualChestInventoryを探します.
	 *
	 * @param int $id
	 * @param Closure $done
	 *
	 * @return VirtualInventory
	 */
	public function findById(int $id, Closure $done) : VirtualInventory;

	/**
	 * 所有者でVirtualChestInventoryを探します.
	 *
	 * @param IPlayer $owner
	 * @param Closure $done
	 *
	 * @return VirtualInventory[]
	 */
	public function findByOwner(IPlayer $owner, Closure $done) : void;

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
	 * @param Closure $done
	 *
	 * @return VirtualInventory
	 */
	public function new(IPlayer $owner, Closure $done) : VirtualInventory;

	/**
	 * VirtualChestInvnentoryをセーブします.
	 *
	 * @param VirtualInventory $inventory
	 */
	public function save(VirtualInventory $inventory) : void;

	/**
	 * Repositoryを終了させます.
	 */
	public function close() : void;
}