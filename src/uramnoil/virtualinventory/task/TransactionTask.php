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

	public function __construct(Closure $async, ?Closure $onDone = null) {
		$this->async = $async;
		$this->onDone = $onDone;
	}

	public function onRun() {
		$async = $this->async;
		if($this->onDone !== null) {
			$this->setResult($async());
		}
	}

	public function onCompletion(Server $server) {
		if($this->onDone !== null) {
			$onDone = $this->onDone;
			$onDone($this->getResult());
		}
	}
}