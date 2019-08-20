<?php


namespace uramnoil\virtualinventory\impersonator;

use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;
use uramnoil\virtualinventory\inventory\VirtualChestInventory;

abstract class Impersonator {
	/** @var Player */
	protected $impersonated;
	/** @var VirtualChestInventory */
	protected $inventory;
	/** @var Position[] */
	protected $replacedPositions = [];
	/** @var Position */
	protected $basedPosition;

	public function __construct(Player $impersonated, VirtualChestInventory $inventory) {
		$this->impersonated = $impersonated;
		$this->inventory = $inventory;
		$this->basedPosition = Position::fromObject($impersonated->floor(), $impersonated->getLevel());
	}

	/**
	 * プレイヤーにダミーのチェストを送ります
	 *
	 * @internal
	 */
	public function impersonate() : void {
		$this->sendChestBlocks();
		$this->sendTilePacket();
		$this->sendContainerPacket();
	}

	/**
	 * ブロックを元に戻します.
	 */
	public function correct() : void {
		foreach($this->replacedPositions as $position) {
			$this->impersonated->level->sendBlocks([$this->impersonated], [$this->basedPosition->getLevel()->getBlock($position)]);
		}
	}

	/**
	 * 偽のチェストを送ります.
	 * 副作用なし.
	 */
	abstract protected function sendChestBlocks() : void;

	/**
	 * プレイヤーにタイルパケットを送ります.
	 */
	abstract protected function sendTilePacket() : void;

	/**
	 * プレイヤーにコンテナを送ります.
	 * WindowTypesに依存してるのでいつか修正します.
	 */
	private function sendContainerPacket() : void {
		$pk = new ContainerOpenPacket();
		$pk->windowId = $this->impersonated->getWindowId($this->inventory);
		$pk->type = WindowTypes::CONTAINER;
		$pk->x = $this->basedPosition->x;
		$pk->y = $this->basedPosition->y;
		$pk->z = $this->basedPosition->z;

		$this->impersonated->dataPacket($pk);

		$this->inventory->sendContents($this->impersonated);
	}
}