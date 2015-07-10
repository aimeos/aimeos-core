<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
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


	/**
	 * Already sent payment e-mails.
	 */
	const EMAIL_PAYMENT = 'email-payment';

	/**
	 * Already sent delivery e-mails.
	 */
	const EMAIL_DELIVERY = 'email-delivery';


	/**
	 * Stock level is already updated.
	 */
	const STOCK_UPDATE = 'stock-update';


	/**
	 * Stock level is already updated.
	 */
	const COUPON_UPDATE = 'coupon-update';
}