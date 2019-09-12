<?php


namespace uramnoil\virtualinventory\repository\extension;


use pocketmine\item\Item;
use pocketmine\Server;
use uramnoil\virtualinventory\inventory\factory\PerpetuatedVirtualInventoryFactory;
use uramnoil\virtualinventory\inventory\PerpetuatedVirtualInventory;
use function assert;

trait InventoryConverterTrait {
	/** @var PerpetuatedVirtualInventoryFactory[] */
	private $factories = [];

	/**
	 * @param  PerpetuatedVirtualInventory  $inventory
	 *
	 * @return array
	 */
	public function inventoryToRaw(PerpetuatedVirtualInventory $inventory) : array {
		$inventoryRaw = [];

		$inventoryRaw['inventory_id'] = $inventory->getId();
		$inventoryRaw['inventory_title'] = $inventory->getTitle();
		$inventoryRaw['items'] = $this->itemsToRaw($inventory->getContents(true));

		return $inventoryRaw;
	}

	/**
	 * @param  array  $inventoryRaw
	 *
	 * @return PerpetuatedVirtualInventory
	 */
	public function rawToInventory(array $inventoryRaw) : PerpetuatedVirtualInventory {
		$id = $inventoryRaw['inventory_id'];
		$title = $inventoryRaw['inventory_title'];
		$owner = Server::getInstance()->getOfflinePlayer($inventoryRaw['owner_name']);
		$inventory = $this->factories[$inventoryRaw['inventory_type']]->createFrom($id, $owner, $title);
		$inventory->setContents($this->rawToItems($inventoryRaw['items']));
		return $inventory;
	}

	/**
	 * @param  array  $items
	 *
	 * @return array
	 *
	 * @see https://github.com/pmmp/PocketMine-MP/blob/a19143cae76ad55f1bdc2f39ad007b1fc170980b/src/pocketmine/item/Item.php#L884
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
	 * @param  array  $raw
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
