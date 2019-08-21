<?php


namespace uramnoil\virtualinventory\repository\extension;


use pocketmine\item\Item;

/**
 * @param array $items
 *
 * @return array
 */
function itemsToRaw(array $items) : array {
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
function rawToItems(array $raw) : array {
	$items = [];
	foreach($raw as $slot => $rawItem) {
		$items[$slot] = Item::jsonDeserialize($rawItem);
	}

	return $items;
}