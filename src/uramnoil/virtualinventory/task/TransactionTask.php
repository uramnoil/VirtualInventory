<?php

namespace uramnoil\virtualinventory\task;

use Closure;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class TransactionTask extends AsyncTask {
	/** @var Closure */
	private $async;
	/** @var Closure|null */
	private $onDone;

	public function __construct(Closure $async, callable $onDone) {
		$this->async = $async;
		$this->onDone = $onDone;
	}

	public function onRun() {
		$async = $this->async;
		$this->setResult($async());
	}

	public function onCompletion(Server $server) {
		$onDone = $this->onDone;
		$onDone($this->getResult());
	}
}