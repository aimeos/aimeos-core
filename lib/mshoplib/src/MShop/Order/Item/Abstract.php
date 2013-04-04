<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 * @version $Id: Abstract.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Base order item class with common constants and methods.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class MShop_Order_Item_Abstract extends MShop_Common_Item_Abstract
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
	 * accept it.
	 */
	const STAT_REFUSED = 6;

	/**
	 * Parcel was returned.
	 * The delivery of the parcel failed because e.g. the address was invalid
	 * and the parcel was returned to the merchant.
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
	 * No e-mail yet.
	 * No e-mail was sent yet. This is the default status after creating the
	 * order.
	 */
	const EMAIL_NONE = 0;

	/**
	 * Order confirmation e-mail.
	 * The order confirmation e-mail was sent.
	 */
	const EMAIL_ACCEPTED = 1;

	/**
	 * Order deleted e-mail.
	 * An e-mail was sent because the delivery of the order was deleted.
	 */
	const EMAIL_DELETED = 2;

	/**
	 * Order pending e-mail.
	 * An e-mail was sent because the order can't be fulfilled yet, because
	 * e.g. one of the products is out of stock.
	 */
	const EMAIL_PENDING = 4;

	/**
	 * Delivery in progress e-mail.
	 * An e-mail was sent because the order is in the filfillment process.
	 */
	const EMAIL_PROGRESS = 8;

	/**
	 * Parcel dispatched e-mail.
	 * An e-mail was sent because the parcel was handed over to the logistic
	 * partner.
	 */
	const EMAIL_DISPATCHED = 16;

	/**
	 * Parcel delivered e-mail.
	 * An e-mail was sent because the parcel was handed over to the customer.
	 */
	const EMAIL_DELIVERED = 32;

	/**
	 * Parcel lost e-mail.
	 * An e-mail was sent because the parcel was lost on the way to the
	 * customer.
	 */
	const EMAIL_LOST = 64;

	/**
	 * Parcel refused e-mail.
	 * An e-mail was sent because the parcel was refused by the customer.
	 */
	const EMAIL_REFUSED = 128;

	/**
	 * Parcel returned e-mail.
	 * An e-mail was sent because the parcel was returned to the merchant.
	 */
	const EMAIL_RETURNED = 256;

	/**
	 * All e-mails were sent.
	 * This constant matches all other e-mail constants.
	 */
	const EMAIL_ALL = 511;


	/**
	 * No flag is set.
	 * This is the default value after the order was created.
	 */
	const FLAG_NONE = 0;

	/**
	 * Product stock and coupon code decreased.
	 * The product stock level was decreased as well as the coupon code count
	 * if a valid coupon code was entered by the customer.
	 */
	const FLAG_STOCK = 1;

	/**
	 * All flags are set.
	 * This constant matches all other flag constants.
	 */
	const FLAG_ALL = 1;


	/**
	 * Tests if the date parameter represents an ISO date format.
	 *
	 * @param string ISO date in yyyy-mm-dd HH:ii:ss format
	 * @throws MShop_Order_Exception If validating the date string failed
	 */
	protected function _checkDateFormat( $date )
	{
		if( preg_match( '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/', $date ) !== 1 ) {
			throw new MShop_Order_Exception( sprintf( 'Invalid characters in date "%1$s". ISO format "yyyy-mm-dd hh:mm:ss" or "yyyy-mm-dd" expected.', $date ) );
		}
	}


	/**
	 * Checks if the given delivery status is a valid constant.
	 *
	 * @param integer $value Delivery status constant defined in MShop_Order_Item_Abstract
	 * @throws MShop_Order_Exception If delivery status is invalid
	 */
	protected function _checkDeliveryStatus( $value )
	{
		$temp = (int) $value;

		if( $temp < MShop_Order_Item_Abstract::STAT_UNFINISHED || $temp > MShop_Order_Item_Abstract::STAT_RETURNED ) {
			throw new MShop_Order_Exception( sprintf( 'Order delivery status "%1$s" not within allowed range', $value ) );
		}
	}


	/**
	 * Checks the given payment status is a valid constant.
	 *
	 * @param integer $value Payment status constant defined in MShop_Order_Item_Abstract
	 * @throws MShop_Order_Exception If payment status is invalid
	 */
	protected function _checkPaymentStatus( $value )
	{
		$temp = (int) $value;

		if( $temp < MShop_Order_Item_Abstract::PAY_UNFINISHED || $temp > MShop_Order_Item_Abstract::PAY_RECEIVED ) {
			throw new MShop_Order_Exception( sprintf( 'Order payment status "%1$s" not within allowed range', $value ) );
		}
	}


	/**
	 * Checks the given order type is a valid constant.
	 *
	 * @param integer $value Type constant defined in MShop_Order_Item_Abstract
	 * @throws MShop_Order_Exception If order type is invalid
	 */
	protected function _checkType( $value )
	{
		switch( $value )
		{
			case MShop_Order_Item_Abstract::TYPE_REPEAT:
			case MShop_Order_Item_Abstract::TYPE_WEB:
			case MShop_Order_Item_Abstract::TYPE_PHONE:
				break;
			default:
				throw new MShop_Order_Exception( sprintf( 'Order type "%1$s" not within allowed range', $value ) );
		}
	}


	/**
	 * Checks the constants for the different status of the email flag.
	 *
	 * @param integer $value Email flag constant
	 * @throws MShop_Order_Exception If email status constant is invalid
	 */
	protected function _checkEmailStatus( $value )
	{
		$temp = (int) $value;

		if( $temp < MShop_Order_Item_Abstract::EMAIL_NONE || $temp > MShop_Order_Item_Abstract::EMAIL_ALL ) {
			throw new MShop_Order_Exception( sprintf( 'Email flags "%1$s" not within allowed range', $value ) );
		}
	}


	/**
	 * Checks the constants for the different internal flag.
	 *
	 * @param integer $value internal flag constant
	 * @throws MShop_Order_Exception If constant is invalid
	 */
	protected function _checkFlag( $value )
	{
		$temp = (int) $value;

		if( $temp < MShop_Order_Item_Abstract::FLAG_NONE || $temp > MShop_Order_Item_Abstract::FLAG_ALL ) {
			throw new MShop_Order_Exception( sprintf( 'Flags "%1$s" not within allowed range', $value ) );
		}
	}
}
