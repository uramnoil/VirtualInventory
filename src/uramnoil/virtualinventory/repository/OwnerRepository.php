<?php


namespace uramnoil\virtualinventory\repository;


use pocketmine\IPlayer;

interface OwnerRepository extends Repository {
	/**
	 * @param IPlayer $player
	 */
	public function new(IPlayer $player) : void;

	/**
	 * @param IPlayer $player
	 */
	public function delete(IPlayer $player) : void;
}