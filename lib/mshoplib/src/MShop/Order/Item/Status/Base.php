<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Status;


/**
 * Abstract class for all order status objects.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class Base
	extends \Aimeos\MShop\Common\Item\Base
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


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'order/status';
	}
}