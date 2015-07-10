<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Price
 */


/**
 * Generic price manager interface for creating and handling prices.
 * @package MShop
 * @subpackage Price
 */
interface MShop_Price_Manager_Interface
	extends MShop_Common_Manager_Factory_Interface
{
	/**
	 * Returns the price item with the lowest price for the given quantity.
	 *
	 * @param array $priceItems List of price items implementing MShop_Price_Item_Interface
	 * @param integer $quantity Number of products
	 * @throws MShop_Price_Exception if no price item is available
	 */
	public function getLowestPrice( array $priceItems, $quantity );
}
