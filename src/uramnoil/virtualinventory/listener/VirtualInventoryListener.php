<?php


namespace uramnoil\virtualinventory\listener;


use uramnoil\virtualinventory\VirtualInventoryPlugin;

abstract class VirtualInventoryListener {
	/** @var VirtualInventoryPlugin  */
	protected $plugin;

	final public function __construct(VirtualInventoryPlugin $plugin) {
		$this->plugin = $plugin;
	}
}
