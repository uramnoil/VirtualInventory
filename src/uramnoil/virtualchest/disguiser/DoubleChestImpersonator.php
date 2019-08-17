<?php


namespace uramnoil\virtualchest\disguiser;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\block\Chest;
use pocketmine\Player;
use uramnoil\virtualchest\inventory\VirtualChestInventory;

class DoubleChestImpersonator extends ChestImpersonator {
	/** @var Chest */
	protected $chest1;
	/** @var Chest */
	protected $chest2;

	public function __construct(Player $impersonated, VirtualChestInventory $inventory) {
		parent::__construct($impersonated, $inventory);
		$this->chest1 = BlockFactory::get(BlockIds::CHEST, null, $this->basedPosition);
		$this->chest2 = $this->chest1->
	}

	protected function sendChestBlocks() : void {
		// TODO: Implement sendChestBlocks() method.
	}

	protected function sendContainerPacket() : void {
		// TODO: Implement sendContainerPacket() method.
	}

	protected function sendTilePacket() : void {
		// TODO: Implement sendTilePacket() method.
	}
}