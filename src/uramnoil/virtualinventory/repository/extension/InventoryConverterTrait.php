<?php


namespace uramnoil\virtualinventory\repository\extension;


use pocketmine\item\Item;
use pocketmine\Server;
use uramnoil\virtualinventory\inventory\factory\VirtualInventoryFactory;
use uramnoil\virtualinventory\inventory\VirtualInventory;
use function assert;
use function strtolower;

trait InventoryConverterTrait {
	/** @var VirtualInventoryFactory[] */
	private $factories = [];

	/**
	 * @param \uramnoil\virtualinventory\inventory\VirtualInventory $inventory
	 *
	 * @return array
	 */
	public function inventoryToRaw(VirtualInventory $inventory) : array {
		$inventoryRaw = [];

		$inventoryRaw['inventory_id'] = $inventory->getId();
		$inventoryRaw['items'] = $this->itemsToRaw($inventory->getContents(true));

		return $inventoryRaw;
	}

	/**
	 * @param array $inventoryRaw
	 *
	 * @return \uramnoil\virtualinventory\inventory\VirtualInventory
	 */
	public function RawToInventory(array $inventoryRaw) : VirtualInventory {
		$id = $inventoryRaw['inventory_id'];
		$owner = Server::getInstance()->getOfflinePlayer($inventoryRaw['owner_name']);
		$inventory = $this->factories[$inventoryRaw['inventory_type']]->createFrom($id, $owner);
		$inventory->setContents($this->rawToItems($inventoryRaw['items']));
		return $inventory;
	}

	/**
	 * @param array $items
	 *
	 * @return array
	 */
	public function itemsToRaw(array $items) : array {
		foreach($items as $item) {
			assert($item instanceof Item);
		}

		$raw = [];

		foreach($items as $slot => $item) {
			$raw[$slot] = $item->jsonSerialize();
		}

		return $raw;
	}

	/**
	 * @param array $raw
	 *
	 * @return Item[]
	 */
	public function rawToItems(array $raw) : array {
		$items = [];
		foreach($raw as $slot => $rawItem) {
			$items[$slot] = Item::jsonDeserialize($rawItem);
		}

		return $items;
	}
}
