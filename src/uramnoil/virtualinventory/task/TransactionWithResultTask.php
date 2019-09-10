<?php


namespace uramnoil\virtualinventory\task;


use pocketmine\Server;

class TransactionWithResultTask extends TransactionTask {
    public function onCompletion(Server $server) {
        $onDone = $this->onDone;
        $onDone($this->getResult());
    }
}