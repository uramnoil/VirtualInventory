<?php


namespace uramnoil\virtualinventory\repository\sqlite\repository\virtualinventory\dao\owner;


use pocketmine\IPlayer;
use uramnoil\virtualinventory\repository\sqlite\repository\virtualinventory\Repository;

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