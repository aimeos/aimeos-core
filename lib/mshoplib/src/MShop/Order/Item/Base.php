<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Order
 */


/**
 * Base order item class with common constants and methods.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class MShop_Order_Item_Base extends MShop_Common_Item_Base
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
	 * Tests if the date parameter represents an ISO date format.
	 *
	 * @param string $date ISO date in yyyy-mm-dd HH:ii:ss format
	 * @throws MShop_Order_Exception If validating the date string failed
	 */
	protected function checkDateFormat( $date )
	{
		if( preg_match( '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/', $date ) !== 1 ) {
			throw new MShop_Order_Exception( sprintf( 'Invalid characters in date "%1$s". ISO format "YYYY-MM-DD hh:mm:ss" expected.', $date ) );
		}
	}


	/**
	 * Checks if the given delivery status is a valid constant.
	 *
	 * @param integer $value Delivery status constant defined in MShop_Order_Item_Base
	 * @throws MShop_Order_Exception If delivery status is invalid
	 */
	protected function checkDeliveryStatus( $value )
	{
		$temp = (int) $value;

		if( $temp < MShop_Order_Item_Base::STAT_UNFINISHED || $temp > MShop_Order_Item_Base::STAT_RETURNED ) {
			throw new MShop_Order_Exception( sprintf( 'Order delivery status "%1$s" not within allowed range', $value ) );
		}
	}


	/**
	 * Checks the given payment status is a valid constant.
	 *
	 * @param integer $value Payment status constant defined in MShop_Order_Item_Base
	 * @throws MShop_Order_Exception If payment status is invalid
	 */
	protected function checkPaymentStatus( $value )
	{
		$temp = (int) $value;

		if( $temp < MShop_Order_Item_Base::PAY_UNFINISHED || $temp > MShop_Order_Item_Base::PAY_RECEIVED ) {
			throw new MShop_Order_Exception( sprintf( 'Order payment status "%1$s" not within allowed range', $value ) );
		}
	}


	/**
	 * Checks the given order type is a valid constant.
	 *
	 * @param integer $value Type constant defined in MShop_Order_Item_Base
	 * @throws MShop_Order_Exception If order type is invalid
	 */
	protected function checkType( $value )
	{
		switch( $value )
		{
			case MShop_Order_Item_Base::TYPE_REPEAT:
			case MShop_Order_Item_Base::TYPE_WEB:
			case MShop_Order_Item_Base::TYPE_PHONE:
				break;
			default:
				throw new MShop_Order_Exception( sprintf( 'Order type "%1$s" not within allowed range', $value ) );
		}
	}
}
