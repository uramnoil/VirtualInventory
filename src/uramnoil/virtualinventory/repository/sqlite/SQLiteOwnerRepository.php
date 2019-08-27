<?php


namespace uramnoil\virtualinventory\repository\sqlite;

use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Utils;
use uramnoil\virtualinventory\extension\SchedulerTrait;
use uramnoil\virtualinventory\repository\OwnerRepository;
use uramnoil\virtualinventory\repository\sqlite\dao\SQLiteOwnerDao;
use uramnoil\virtualinventory\task\TransactionTask;

class SQLiteOwnerRepository implements OwnerRepository {
	use SchedulerTrait;

	/** @var PluginBase */
	private $plugin;
	private $db;

	public function __construct(PluginBase $plugin) {
		$this->plugin = $plugin;
		$this->db = new SQLiteOwnerDao($plugin);
	}

	public function open() : void {
		$this->db->open();
	}

	public function close() : void {
		$this->db->close();
	}

	public function new(IPlayer $player, ?callable $onDone) : void {
		Utils::validateCallableSignature(function(?object $noUse) : void {}, $onDone);

		$task = new TransactionTask(function() use($player) : void{
			$this->db->create($player->getName());
		}, function(?object $noUse) : void {});

		$this->submitTask($task);
	}

	public function delete(IPlayer $player, ?callable $onDone) : void {
		isset($onDone) ?
			Utils::validateCallableSignature(function(?object $noUse) {}, $$onDone)
			: $onDone = function(?object $noUse) {};

		$task = new TransactionTask(function() use($player) : void {
			$this->db->delete($player->getName());
		}, function(?object $noUse) : void {});

		$this->submitTask($task);
	}

	public function exists(IPlayer $player, callable $onDone) : void {
		Utils::validateCallableSignature(function(bool $exists) : void {}, $onDone);

		$task = new TransactionTask(function() use($player) : bool {
			return $this->db->exists($player);
		}, $onDone);

		$this->submitTask($task);
	}
}