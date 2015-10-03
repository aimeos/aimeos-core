<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Order
 */


/**
 * Basket item abstract class defining available flags.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class MShop_Order_Item_Base_Product_Base extends MShop_Order_Item_Base
{
	/**
	 * No flag used.
	 * No order product flag set.
	 */
	const FLAG_NONE = 0;

	/**
	 * Product is immutable.
	 * Ordered product can't be modifed or deleted by the customer because it
	 * was e.g. added by a coupon provider.
	 */
	const FLAG_IMMUTABLE = 1;


	/**
	 * Checks if the given flag constant is valid.
	 *
	 * @param integer $value Flag constant value
	 */
	protected function checkFlags( $value )
	{
		$value = (int) $value;

		if( $value < MShop_Order_Item_Base_Product_Base::FLAG_NONE ||
			$value > MShop_Order_Item_Base_Product_Base::FLAG_IMMUTABLE ) {
				throw new MShop_Order_Exception( sprintf( 'Flags "%1$s" not within allowed range', $value ) );
		}
	}
}
