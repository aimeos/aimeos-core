<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item;


/**
 * Base order item class with common constants and methods.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class Base extends \Aimeos\MShop\Common\Item\Base
{
	/**
	 * Repeated order.
	 * The order is created automatically based on an existing order of the
	 * customer.
	 */
	const TYPE_REPEAT = 'repeat';

	/**
	 * Web/internet order.
	 * The order is created manually by the customer using the web shop.
	 */
	const TYPE_WEB = 'web';

	/**
	 * Phone order.
	 * The order is created manually by an operator talking to the customer
	 * over the phone.
	 */
	const TYPE_PHONE = 'phone';


	/**
	 * Unfinished delivery.
	 * This is the default status after creating an order and this status
	 * should be also used as long as technical errors occurs.
	 */
	const STAT_UNFINISHED = -1;

	/**
	 * Delivery was deleted.
	 * The delivery of the order was deleted manually.
	 */
	const STAT_DELETED = 0;

	/**
	 * Delivery is pending.
	 * The order is not yet in the fulfillment process until further actions
	 * are taken.
	 */
	const STAT_PENDING = 1;

	/**
	 * Fulfillment in progress.
	 * The delivery of the order is in the (internal) fulfillment process and
	 * will be ready soon.
	 */
	const STAT_PROGRESS = 2;

	/**
	 * Parcel is dispatched.
	 * The parcel was given to the logistic partner for delivery to the
	 * customer.
	 */
	const STAT_DISPATCHED = 3;

	/**
	 * Parcel was delivered.
	 * The logistic partner delivered the parcel and the customer received it.
	 */
	const STAT_DELIVERED = 4;

	/**
	 * Parcel is lost.
	 * The parcel is lost during delivery by the logistic partner and haven't
	 * reached the customer nor it's returned to the merchant.
	 */
	const STAT_LOST = 5;

	/**
	 * Parcel was refused.
	 * The delivery of the parcel failed because the customer has refused to
	 * accept it or the address was invalid.
	 */
	const STAT_REFUSED = 6;

	/**
	 * Parcel was returned.
	 * The parcel was sent back by the customer.
	 */
	const STAT_RETURNED = 7;


	/**
	 * Unfinished payment.
	 * This is the default status after creating an order and this status
	 * should be also used as long as technical errors occurs.
	 */
	const PAY_UNFINISHED = -1;

	/**
	 * Payment was deleted.
	 * The payment for the order was deleted manually.
	 */
	const PAY_DELETED = 0;

	/**
	 * Payment was canceled.
	 * The customer canceled the payment process.
	 */
	const PAY_CANCELED = 1;

	/**
	 * Payment was refused.
	 * The customer didn't enter valid payment details.
	 */
	const PAY_REFUSED = 2;

	/**
	 * Payment was refund.
	 * The payment was OK but refund and the customer got his money back.
	 */
	const PAY_REFUND = 3;

	/**
	 * Payment is pending.
	 * The payment is not yet done until further actions are taken.
	 */
	const PAY_PENDING = 4;

	/**
	 * Payment is authorized.
	 * The customer authorized the merchant to invoice the amount but the money
	 * is not yet received. This is used for all post-paid orders.
	 */
	const PAY_AUTHORIZED = 5;

	/**
	 * Payment is received.
	 * The merchant received the money from the customer.
	 */
	const PAY_RECEIVED = 6;


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'order';
	}


	/**
	 * Checks if the given delivery status is a valid constant.
	 *
	 * @param integer $value Delivery status constant defined in \Aimeos\MShop\Order\Item\Base
	 * @throws \Aimeos\MShop\Order\Exception If delivery status is invalid
	 */
	protected function checkDeliveryStatus( $value )
	{
		if( $value < \Aimeos\MShop\Order\Item\Base::STAT_UNFINISHED || $value > \Aimeos\MShop\Order\Item\Base::STAT_RETURNED ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Order delivery status "%1$s" not within allowed range', $value ) );
		}

		return (int) $value;
	}


	/**
	 * Checks the given payment status is a valid constant.
	 *
	 * @param integer $value Payment status constant defined in \Aimeos\MShop\Order\Item\Base
	 * @throws \Aimeos\MShop\Order\Exception If payment status is invalid
	 */
	protected function checkPaymentStatus( $value )
	{
		if( $value < \Aimeos\MShop\Order\Item\Base::PAY_UNFINISHED || $value > \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Order payment status "%1$s" not within allowed range', $value ) );
		}

		return (int) $value;
	}
}
