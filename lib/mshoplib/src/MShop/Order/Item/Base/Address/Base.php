<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Order
 */


/**
 * Abstract class with constants for all order address items.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class MShop_Order_Item_Base_Address_Base extends MShop_Common_Item_Address_Base
{
	/**
	 * Delivery address.
	 */
	const TYPE_DELIVERY = 'delivery';

	/**
	 * Billing address.
	 */
	const TYPE_PAYMENT = 'payment';


	/**
	 * Checks if the given address type is valid
	 *
	 * @param string $value Address type defined in MShop_Order_Item_Base_Address_Base
	 * @throws MShop_Order_Exception If type is invalid
	 */
	protected function checkType( $value )
	{
		switch( $value )
		{
			case MShop_Order_Item_Base_Address_Base::TYPE_DELIVERY:
			case MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT:
				return;
			default:
				throw new MShop_Order_Exception( sprintf( 'Address type "%1$s" not within allowed range', $value ) );
		}
	}
}
