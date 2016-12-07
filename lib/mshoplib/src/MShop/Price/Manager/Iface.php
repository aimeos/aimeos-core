<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	extends \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/**
	 * Returns the price item with the lowest price for the given quantity.
	 *
	 * @param array $priceItems List of price items implementing \Aimeos\MShop\Price\Item\Iface
	 * @param integer $quantity Number of products
	 * @return \Aimeos\MShop\Price\Iface Price item with the lowest price
	 * @throws \Aimeos\MShop\Price\Exception if no price item is available
	 */
	public function getLowestPrice( array $priceItems, $quantity );
}
