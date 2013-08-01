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


	/**
	 * Order accepted e-mail is already sent.
	 */
	const EMAIL_ACCEPTED = 'email-accepted';

	/**
	 * Order deleted e-mail is already sent.
	 */
	const EMAIL_DELETED = 'email-deleted';

	/**
	 * Order pending e-mail is already sent.
	 */
	const EMAIL_PENDING = 'email-pending';

	/**
	 * Order "in progress" e-mail is already sent.
	 */
	const EMAIL_PROGRESS = 'email-progress';

	/**
	 * Order dispatched e-mail is already sent.
	 */
	const EMAIL_DISPATCHED = 'email-dispatched';

	/**
	 * Order delivered e-mail is already sent.
	 */
	const EMAIL_DELIVERED = 'email-delivered';

	/**
	 * Order lost e-mail is already sent.
	 */
	const EMAIL_LOST = 'email-lost';

	/**
	 * Order refused e-mail is already sent.
	 */
	const EMAIL_REFUSED = 'email-refused';

	/**
	 * Order returned e-mail is already sent.
	 */
	const EMAIL_RETURNED = 'email-returned';


	/**
	 * Stock level is already updated.
	 */
	const STOCK_UPDATE = 'stock-update';
}