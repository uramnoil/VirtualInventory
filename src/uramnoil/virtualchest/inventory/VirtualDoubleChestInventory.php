<?php


namespace uramnoil\virtualchest\inventory;


use pocketmine\Player;
use uramnoil\virtualchest\impersonator\ChestImpersonator;
use uramnoil\virtualchest\impersonator\DoubleChestImpersonator;

class VirtualDoubleChestInventory extends VirtualChestInventory {
	public function getName() : string {
		return "Virtual Double Chest Inventory";
	}

	public function getDefaultSize() : int {
		return 54;
	}

	public function createImpersonatorFrom(Player $impersonated) : ChestImpersonator {
		return new DoubleChestImpersonator($impersonated, $this);
	}
}