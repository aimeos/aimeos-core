<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Price
 */


namespace Aimeos\MShop\Price\Manager;


/**
 * Generic price manager interface for creating and handling prices.
 * @package MShop
 * @subpackage Price
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MShop\Common\Manager\ListRef\Iface
{
	/**
	 * Returns the price item with the lowest price for the given quantity.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface[] $priceItems List of price items
	 * @param int $quantity Number of products
	 * @return \Aimeos\MShop\Price\Item\Iface Price item with the lowest price
	 * @throws \Aimeos\MShop\Price\Exception if no price item is available
	 */
	public function getLowestPrice( array $priceItems, int $quantity ) : \Aimeos\MShop\Price\Item\Iface;

	/**
	 * Saves a price item object.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $item Price item object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Price\Item\Iface Updated item including the generated ID
	 * @throws \Aimeos\MShop\Price\Exception If price couldn't be saved
	 */
	public function saveItem( \Aimeos\MShop\Price\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Price\Item\Iface;
}
