<?php

namespace uramnoil\virtualinventory\repository;

use Closure;
use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use uramnoil\virtualinventory\inventory\VirtualInventory;

interface VirtualInventoryRepository {
	public function __construct(PluginBase $plugin);

	/**
	 * IDでVirtualChestInventoryを探します.
	 *
	 * @param int 	  $id
	 * @param Closure $onDone
	 */
	public function findById(int $id, Closure $onDone) : void;

	/**
	 * 所有者でVirtualChestInventoryを探します.
	 *
	 * @param IPlayer $owner
	 * @param Closure $onDone
	 */
	public function findByOwner(IPlayer $owner, Closure $onDone) : void;

	/**
	 * VirtualChestInventoryをリポジトリから削除します.
	 *
	 * @param VirtualInventory $inventory
	 * @param Closure|null     $onDone
	 */
	public function delete(VirtualInventory $inventory, ?Closure $onDone) : void;

	/**
	 * 新しいVirtualChestInventoryを作成します.
	 *
	 * @param IPlayer $owner
	 * @param int	  $inventoryType
	 * @param Closure $onDone
	 */
	public function new(IPlayer $owner, int $inventoryType = InventoryIds::INVENTORY_TYPE_CHEST, ?Closure $onDone = null) : void;

	/**
	 * VirtualChestInventoryをセーブします.
	 *
	 * @param VirtualInventory $inventory
	 * @param Closure		   $onDone
	 */
	public function save(VirtualInventory $inventory, ?Closure $onDone = null) : void;

	/**
	 * Repositoryを終了させます.
	 */
	public function close() : void;
}