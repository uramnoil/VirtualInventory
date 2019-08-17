<?php


namespace uramnoil\virtualchest\disguiser;

use pocketmine\level\Position;
use pocketmine\Player;
use uramnoil\virtualchest\inventory\VirtualChestInventory;

abstract class ChestImpersonator {

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
	 * コンテナを送る際は１Tickのみ遅延させてください.
	 */
	abstract protected function sendContainerPacket() : void;
}