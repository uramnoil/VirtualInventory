<?php


namespace uramnoil\virtualinventory\repository\dao;


use pocketmine\plugin\PluginBase;

interface DAO {
	public function __construct(PluginBase $plugin);
	/**
	 * データベースに接続します.
	 */
	public function open() : void;

	/**
	 * データベースを切断します.
	 */
	public function close() : void;
}