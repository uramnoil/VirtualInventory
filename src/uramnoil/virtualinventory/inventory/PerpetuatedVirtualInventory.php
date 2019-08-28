<?php


namespace uramnoil\virtualinventory\inventory;


use pocketmine\IPlayer;
use uramnoil\virtualinventory\repository\VirtualInventoryRepository;

abstract class PerpetuatedVirtualInventory extends VirtualInventory {
	/** @var VirtualInventoryRepository */
	private $repository;
	/** @var int */
	protected $id;
	/** @var IPlayer */
	protected $owner;

	public function __construct(VirtualInventoryRepository $repository, int $id, IPlayer $owner, $items = [], int $size = null, string $title = null) {
		parent::__construct($items, $size, $title);
		$this->repository = $repository;
		$this->id = $id;
		$this->owner = $owner;
	}

	/**
	 * @return int
	 */
	public final function getId() : int {
		return $this->id;
	}

	/**
	 * @return IPlayer
	 */
	public final function getOwner() : IPlayer {
		return $this->owner;
	}
}