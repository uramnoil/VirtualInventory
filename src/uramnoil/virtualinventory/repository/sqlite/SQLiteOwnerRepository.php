<?php


namespace uramnoil\virtualinventory\repository\sqlite;

use pocketmine\IPlayer;
use pocketmine\utils\Utils;
use SQLite3;
use uramnoil\virtualinventory\extension\SchedulerTrait;
use uramnoil\virtualinventory\repository\OwnerRepository;
use uramnoil\virtualinventory\repository\sqlite\dao\SQLiteOwnerDao;
use uramnoil\virtualinventory\task\TransactionTask;

class SQLiteOwnerRepository implements OwnerRepository {
	use SchedulerTrait;
	/** @var SQLiteOwnerDao */
	private $dao;

	public function __construct(SQLite3 $db) {
		$this->dao = new SQLiteOwnerDao($db);
	}

	public function close() : void {
		$this->dao->close();
	}

	public function new(IPlayer $player, ?callable $onDone) : void {
		Utils::validateCallableSignature(function(?object $noUse) : void {}, $onDone);

		$task = new TransactionTask(function() use($player) : void{
			$this->dao->create($player->getName());
		}, function(?object $noUse) : void {});

		$this->submitTask($task);
	}

	public function delete(IPlayer $player, ?callable $onDone) : void {
		isset($onDone) ?
			Utils::validateCallableSignature(function(?object $noUse) {}, $$onDone)
			: $onDone = function(?object $noUse) {};

		$task = new TransactionTask(function() use($player) : void {
			$this->dao->delete($player->getName());
		}, function(?object $noUse) : void {});

		$this->submitTask($task);
	}

	public function exists(IPlayer $player, callable $onDone) : void {
		Utils::validateCallableSignature(function(bool $exists) : void {}, $onDone);

		$task = new TransactionTask(function() use($player) : bool {
			return $this->dao->exists($player);
		}, $onDone);

		$this->submitTask($task);
	}
}