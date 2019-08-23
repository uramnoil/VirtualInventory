<?php

namespace uramnoil\virtualinventory\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;

class RegisterOwnerListener extends VirtualInventoryListener implements Listener {

	/**
	 * @ignoreCancel true
	 * @priority	 HIGHEST
	 *
	 * @param PlayerPreLoginEvent $event
	 */
	public function onPlayerPreLogin(PlayerPreLoginEvent $event) : void {
		if($event->getPlayer()->hasPlayedBefore()) {
			$this->plugin->getAPI()->registerOwner($event->getPlayer());
		}
	}
}