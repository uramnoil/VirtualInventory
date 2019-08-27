<?php


namespace uramnoil\virtualinventory\repository;


use pocketmine\IPlayer;

interface OwnerRepository extends Repository {
	/**
	 * @param IPlayer       $player
	 * @param callable|null $onDone
	 */
	public function new(IPlayer $player, ?callable $onDone) : void;

	/**
	 * @param IPlayer  $player
	 * @param callable $onDone
	 */
	public function delete(IPlayer $player, ?callable $onDone) : void;

	public function exists(IPlayer $player, callable $onDone) : void;
}