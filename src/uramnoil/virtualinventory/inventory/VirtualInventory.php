<?php


namespace uramnoil\virtualinventory\inventory;

use pocketmine\inventory\BaseInventory;
use pocketmine\IPlayer;
use pocketmine\Player;
use uramnoil\virtualinventory\impersonator\Impersonator;
use uramnoil\virtualinventory\impersonator\ChestImpersonator;
use uramnoil\virtualinventory\repository\VirtualInventoryRepository;
use function spl_object_hash;

abstract class VirtualInventory extends BaseInventory {
	/** @var VirtualInventoryRepository */
	private $repository;
	/** @var ChestImpersonator[] */
	private $impersonators = [];

	/** @var int */
	protected $id;
	/** @var IPlayer */
	protected $owner;

	/**
	 * VirtualChestInventory constructor.
	 *
	 * @param VirtualInventoryRepository $repository
	 * @param int                                                              $id
	 * @param IPlayer                                              $owner
	 * @param array                                                            $items
	 * @param int|null                                                         $size
	 * @param string|null                                                      $title
	 */
	public function __construct(VirtualInventoryRepository $repository, int $id, IPlayer $owner, $items = [], int $size = null, string $title = null) {
		parent::__construct($items, $size, $title);
		$this->repository = $repository;
		$this->id = $id;
		$this->owner = $owner;
	}

	public function getId() : int {
		return $this->id;
	}

	/**
	 * VirtualChestInventoryが削除されたとき.
	 */
	public function onDelete() : void {
		$this->removeAllViewers(true);
	}

	public function onOpen(Player $who) : void {
		parent::onClose($who);
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