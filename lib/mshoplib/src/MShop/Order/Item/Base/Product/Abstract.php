<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Basket item abstract class defining available flags.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class MShop_Order_Item_Base_Product_Abstract extends MShop_Order_Item_Abstract
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


	protected function _checkFlags($value)
	{
		$value = (int) $value;

		if( $value < MShop_Order_Item_Base_Product_Abstract::FLAG_NONE ||
			$value > MShop_Order_Item_Base_Product_Abstract::FLAG_IMMUTABLE ) {
				throw new MShop_Order_Exception( sprintf( 'Flags "%1$s" not within allowed range', $value ) );
		}
	}
}
