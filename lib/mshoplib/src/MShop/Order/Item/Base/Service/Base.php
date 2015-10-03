<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Order
 */


/**
 * Abstract class MShop_Order_Item_Base_Service_Base.
 * @package MShop
 * @subpackage Order
 */
abstract class MShop_Order_Item_Base_Service_Base
	extends MShop_Common_Item_Base
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
				throw new MShop_Order_Exception( sprintf( 'Service of type "%1$s" not available', $value ) );
		}
	}
}
