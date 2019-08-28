<?php


namespace uramnoil\virtualinventory\repository\dao;


interface DAO {
	/**
	 * データベースを切断します.
	 */
	public function close() : void;
}