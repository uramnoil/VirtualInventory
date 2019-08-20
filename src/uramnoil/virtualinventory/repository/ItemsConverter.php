<?php


namespace uramnoil\virtualinventory\repository;


use pocketmine\item\Item;

trait ItemsConverter {
	/**
	 * @param Item[] $items
	 *
	 * @return array
	 */
	public function itemsToRaw(array $items) : array {
		foreach($items as $item) {
			assert($item instanceof Item);
		}

		$raw = [];

		foreach($items as $item) {
			$raw[] = $item->jsonSerialize();
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
		foreach($raw as $rawItem) {
			$items[] = Item::jsonDeserialize($rawItem);
		}

		return $items;
	}
}