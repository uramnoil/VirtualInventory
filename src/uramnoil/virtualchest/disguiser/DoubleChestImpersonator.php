<?php


namespace uramnoil\virtualchest\disguiser;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\BlockEntityDataPacket;
use pocketmine\Player;
use uramnoil\virtualchest\inventory\VirtualChestInventory;
use pocketmine\tile\Chest;

class DoubleChestImpersonator extends ChestImpersonator {
	/** @var Chest */
	protected $chest1;
	/** @var Chest */
	protected $chest2;

	public function __construct(Player $impersonated, VirtualChestInventory $inventory) {
		parent::__construct($impersonated, $inventory);
		$this->chest1 = BlockFactory::get(BlockIds::CHEST, null, $this->basedPosition);
		$this->chest2 = BlockFactory::get(BlockIds::CHEST)->setComponents(
			$this->basedPosition->x + 1,
			$this->basedPosition->y,
			$this->basedPosition->z
		);
	}

	protected function sendChestBlocks() : void {
		$this->impersonated->level->sendBlocks([$this->impersonated], [$this->chest1]);
		$this->impersonated->level->sendBlocks([$this->impersonated], [$this->chest2]);

		$this->replacedPositions[] = $this->chest1;
		$this->replacedPositions[] = $this->chest2;
	}

	protected function sendTilePacket() : void {
		$writer = new NetworkLittleEndianNBTStream();

		//$chest1
		$pk = new BlockEntityDataPacket();
		$pk->x = $this->chest1->x;
		$pk->y = $this->chest1->y;
		$pk->z = $this->chest1->z;

		$nbt = new CompoundTag();
		$nbt->setInt(Chest::TAG_PAIRX, $this->chest2->x);
		$nbt->setInt(Chest::TAG_PAIRZ, $this->chest2->z);

		$pk->namedtag = $writer->write($nbt);

		$this->impersonated->dataPacket($pk);

		//chest2
		$pk = new BlockEntityDataPacket();
		$pk->x = $this->chest2->x;
		$pk->y = $this->chest2->y;
		$pk->z = $this->chest2->z;

		$nbt = new CompoundTag();
		$nbt->setInt(Chest::TAG_PAIRX, $this->chest1->x);
		$nbt->setInt(Chest::TAG_PAIRZ, $this->chest1->z);

		$pk->namedtag = $writer->write($nbt);

		$this->impersonated->dataPacket($pk);
	}
}