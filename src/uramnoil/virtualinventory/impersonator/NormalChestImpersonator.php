<?php

namespace uramnoil\virtualinventory\impersonator;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\BlockEntityDataPacket;
use pocketmine\tile\Tile;

class NormalChestImpersonator extends Impersonator {
	protected function sendChestBlocks() : void {
		$chest = BlockFactory::get(BlockIds::CHEST, null, $this->basedPosition);
		$this->replacedPositions[] = $this->basedPosition;
		$this->impersonated->level->sendBlocks([$this->impersonated], [$chest]);
	}

	protected function sendTilePacket() : void {
		$nbt = new CompoundTag("", [
			new StringTag(Tile::TAG_ID, Tile::CHEST),
			new IntTag(Tile::TAG_X, $this->basedPosition->x),
			new IntTag(Tile::TAG_Y, $this->basedPosition->y),
			new IntTag(Tile::TAG_Z, $this->basedPosition->z),
		]);

		$pk = new BlockEntityDataPacket();
		$pk->x = $this->basedPosition->x;
		$pk->y = $this->basedPosition->y;
		$pk->z = $this->basedPosition->z;
		$pk->namedtag = (new NetworkLittleEndianNBTStream())->write($nbt);

		$this->impersonated->dataPacket($pk);
	}
}