<?php


namespace uramnoil\virtualchest\inventory;

use pocketmine\Player;
use uramnoil\virtualchest\impersonator\ChestImpersonator;
use uramnoil\virtualchest\impersonator\NormalChestImpersonator;

class VirtualNormalChestInventory extends VirtualChestInventory {
	public function getName() : string {
		return "Virtual Normal Chest";
	}

	public function getDefaultSize() : int {
		return 27;
	}

	public function createImpersonatorFrom(Player $impersonated) : ChestImpersonator {
		return new NormalChestImpersonator($impersonated, $this);
	}
}