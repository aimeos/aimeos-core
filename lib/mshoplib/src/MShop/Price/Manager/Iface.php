<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	extends \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MShop\Common\Manager\ListsRef\Iface
{
	/**
	 * Returns the price item with the lowest price for the given quantity.
	 *
	 * @param \Aimeos\Map $priceItems List of price items implementing \Aimeos\MShop\Price\Item\Iface
	 * @param float $quantity Number of products
	 * @return \Aimeos\MShop\Price\Item\Iface Price item with the lowest price
	 * @throws \Aimeos\MShop\Price\Exception if no price item is available
	 */
	public function getLowestPrice( \Aimeos\Map $priceItems, float $quantity ) : \Aimeos\MShop\Price\Item\Iface;
}
