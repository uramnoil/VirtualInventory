<?php


namespace uramnoil\virtualinventory\repository\dao;


interface Transactionable {

	/**
	 * トランザクションを開始します.
	 */
	public function begin() : void;

	/**
	 * トランザクションをコミットさせます.
	 */
	public function commit() : void;

	/**
	 * トランザクションをロールバックします.
	 */
	public function rollback() : void;
}