<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Abstract class for all order status objects.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class MShop_Order_Item_Status_Abstract
	extends MShop_Common_Item_Abstract
{
	/**
	 * Payment status.
	 */
	const STATUS_PAYMENT = 'status-payment';

	/**
	 * Delivery status.
	 */
	const STATUS_DELIVERY = 'status-delivery';
}