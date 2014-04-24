<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Abstract class MShop_Order_Item_Base_Service_Abstract.
 * @package MShop
 * @subpackage Order
 */
abstract class MShop_Order_Item_Base_Service_Abstract
	extends MShop_Common_Item_Abstract
	implements MShop_Order_Item_Base_Service_Interface
{
	/**
	 * Delivery service.
	 */
	const TYPE_DELIVERY = 'delivery';

	/**
	 * Payment service.
	 */
	const TYPE_PAYMENT = 'payment';


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
			case MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT:
				return;
			default:
				throw new MShop_Order_Exception( sprintf( 'Service type "%1$s" is unknown', $value ) );
		}
	}
}
