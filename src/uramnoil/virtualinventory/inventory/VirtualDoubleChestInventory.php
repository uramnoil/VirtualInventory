<?php


namespace uramnoil\virtualinventory\inventory;


use pocketmine\Player;
use uramnoil\virtualinventory\impersonator\Impersonator;
use uramnoil\virtualinventory\impersonator\DoubleChestImpersonator;

class VirtualDoubleChestInventory extends VirtualChestInventory {
	public function getName() : string {
		return "Virtual Double Chest Inventory";
	}

	public function getDefaultSize() : int {
		return 54;
	}

	public function createImpersonatorFrom(Player $impersonated) : Impersonator {
		return new DoubleChestImpersonator($impersonated, $this);
	}
}