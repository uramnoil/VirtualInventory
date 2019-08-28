<?php


namespace uramnoil\virtualinventory\repository;

interface Repository {

	/**
	 * Repositoryを終了させます.
	 */
	public function close() : void;
}