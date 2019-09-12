<?php


namespace uramnoil\virtualinventory\inventory;

use pocketmine\inventory\BaseInventory;
use pocketmine\Player;
use uramnoil\virtualinventory\inventory\impersonator\Impersonator;
use uramnoil\virtualinventory\inventory\impersonator\ChestImpersonator;
use function spl_object_hash;

abstract class VirtualInventory extends BaseInventory {
	/** @var ChestImpersonator[] */
	private $impersonators = [];

	/**
	 * VirtualChestInventoryが削除されたとき.
	 */
	public function onDelete() : void {
		$this->removeAllViewers(true);
	}

	public function onOpen(Player $who) : void {
		parent::onOpen($who);
		$this->impersonators[spl_object_hash($who)] = $this->createImpersonatorFrom($who);
		$this->impersonators[spl_object_hash($who)]->impersonate();
	}

	public function onClose(Player $who) : void {
		parent::onClose($who);
		$this->impersonators[spl_object_hash($who)]->correct();
		unset($this->impersonators[spl_object_hash($who)]);
	}

	abstract public function createImpersonatorFrom(Player $impersonated) : Impersonator;
}