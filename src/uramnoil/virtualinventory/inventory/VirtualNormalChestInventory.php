<?php


namespace uramnoil\virtualinventory\inventory;

use pocketmine\Player;
use uramnoil\virtualinventory\impersonator\Impersonator;
use uramnoil\virtualinventory\impersonator\NormalChestImpersonator;

class VirtualNormalChestInventory extends VirtualChestInventory {
	public function getName() : string {
		return "Virtual Normal Chest";
	}

	public function getDefaultSize() : int {
		return 27;
	}

	public function createImpersonatorFrom(Player $impersonated) : Impersonator {
		return new NormalChestImpersonator($impersonated, $this);
	}
}