<?php


namespace uramnoil\virtualchest\inventory;

use pocketmine\inventory\BaseInventory;
use pocketmine\IPlayer;
use pocketmine\Player;
use uramnoil\virtualchest\disguiser\ChestImpersonatorFactory;
use uramnoil\virtualchest\disguiser\NormalChestImpersonator;
use uramnoil\virtualchest\repository\VirtualChestInventoryRepository;
use function spl_object_hash;

abstract class VirtualChestInventory extends BaseInventory {
	/** @var VirtualChestInventoryRepository */
	private $repository;
	/** @var NormalChestImpersonator[] */
	private $disguisers = [];
	/** @var ChestImpersonatorFactory */
	private $disguiserFactory;

	/** @var int */
	protected $id;
	/** @var IPlayer */
	protected $owner;

	/**
	 * VirtualChestInventory constructor.
	 *
	 * @param \uramnoil\virtualchest\repository\VirtualChestInventoryRepository $repository
	 * @param \uramnoil\virtualchest\disguiser\ChestImpersonatorFactory         $factory
	 * @param int                                                               $id
	 * @param \pocketmine\IPlayer                                               $owner
	 * @param array                                                             $items
	 * @param int|null                                                          $size
	 * @param string|null                                                       $title
	 */
	public function __construct(VirtualChestInventoryRepository $repository, ChestImpersonatorFactory $factory, int $id, IPlayer $owner, $items = [], int $size = null, string $title = null) {
		parent::__construct($items, $size, $title);
		$this->repository = $repository;
		$this->disguiserFactory = $factory;
		$this->id = $id;
		$this->owner = $owner;
	}

	/**
	 * VirtualChestInventoryが削除されたとき.
	 */
	public function onDelete() : void {
		$this->removeAllViewers(true);
	}

	public function onOpen(Player $who) : void {
		parent::onClose($who);
		$this->disguisers[spl_object_hash($who)] = $this->disguiserFactory->create();
		$this->disguisers[spl_object_hash($who)]->impersonate();
	}

	public function onClose(Player $who) : void {
		parent::onClose($who);
		$this->disguisers[spl_object_hash($who)]->correct();
		unset($this->disguisers[spl_object_hash($who)]);
	}
}