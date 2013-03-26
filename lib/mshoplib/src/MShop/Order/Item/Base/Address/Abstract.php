<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 * @version $Id: Abstract.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Abstract class with constants for all order address items.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class MShop_Order_Item_Base_Address_Abstract extends MShop_Common_Item_Address_Abstract
{
	/**
	 * Delivery address.
	 */
	const TYPE_DELIVERY = 'delivery';

	/**
	 * Billing address.
	 */
	const TYPE_BILLING = 'payment';


	/**
	 * Checks if the given address type is valid
	 *
	 * @param string $value Address type defined in MShop_Order_Item_Base_Address_Abstract
	 * @throws MShop_Order_Exception If type is invalid
	 */
	protected function _checkType( $value )
	{
		switch( $value )
		{
			case MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY:
			case MShop_Order_Item_Base_Address_Abstract::TYPE_BILLING:
				return;
			default:
				throw new MShop_Order_Exception( sprintf( 'An error occured in the order. Address type "%1$s" not within allowed range.', $value ) );
		}
	}
}
