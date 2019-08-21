<?php


namespace uramnoil\virtualinventory\repository\extension;


use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

trait SchedulerTrait {
	protected function submitTask(AsyncTask $task) {
		Server::getInstance()->getAsyncPool()->submitTask($task);
	}
}