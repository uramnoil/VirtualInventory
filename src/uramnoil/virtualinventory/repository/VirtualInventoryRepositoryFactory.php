<?php


namespace uramnoil\virtualinventory\repository;


class VirtualInventoryRepositoryFactory {
	public function create() : VirtualInventoryRepository {
		return MockVirtualInventoryRepository();
	}
}