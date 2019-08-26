<?php


namespace uramnoil\virtualinventory\repository;


use pocketmine\plugin\PluginBase;

interface Repository {
	/**
	 * @param PluginBase $plugin
	 */
	public function __construct(PluginBase $plugin);

	/**
	 * Repositoryを起動します.
	 */
	public function open() : void;

	/**
	 * Repositoryを終了させます.
	 */
	public function close() : void;
}