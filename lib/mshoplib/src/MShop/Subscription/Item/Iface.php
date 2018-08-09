<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 * @package MShop
 * @subpackage Subscription
 */


namespace Aimeos\MShop\Subscription\Item;


/**
 * Interface for all order item implementations.
 *
 * @package MShop
 * @subpackage Subscription
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface
{
	/**
	 * Renewing payment failed
	 */
	const REASON_PAYMENT = -1;

	/**
	 * Subscription ended normally
	 */
	const REASON_END = 0;

	/**
	 * Subscription cancelled by customer
	 */
	const REASON_CANCEL = 1;


	/**
	 * Returns the ID of the base order
	 *
	 * @return string ID of the base order
	 */
	public function getOrderBaseId();

	/**
	 * Sets the ID of the base order item which the customer bought
	 *
	 * @param string $id ID of the base order
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setOrderBaseId( $id );

	/**
	 * Returns the ID of the ordered product
	 *
	 * @return string ID of the ordered product
	 */
	public function getOrderProductId();

	/**
	 * Sets the ID of the ordered product item which the customer subscribed for
	 *
	 * @param string $id ID of the ordered product
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setOrderProductId( $id );

	/**
	 * Returns the date of the next subscription renewal
	 *
	 * @return string ISO date in "YYYY-MM-DD HH:mm:ss" format
	 */
	public function getDateNext();

	/**
	 * Sets the date of the next subscription renewal
	 *
	 * @param string $date ISO date in "YYYY-MM-DD HH:mm:ss" format
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setDateNext( $date );

	/**
	 * Returns the date when the subscription renewal ends
	 *
	 * @return string|null ISO date in "YYYY-MM-DD HH:mm:ss" format
	 */
	public function getDateEnd();

	/**
	 * Sets the delivery date of the invoice.
	 *
	 * @param string|null $date ISO date in "YYYY-MM-DD HH:mm:ss" format
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setDateEnd( $date );

	/**
	 * Returns the time interval to pass between the subscription renewals
	 *
	 * @return string PHP time interval, e.g. "P1M2W"
	 */
	public function getInterval();

	/**
	 * Sets the time interval to pass between the subscription renewals
	 *
	 * @param string $value PHP time interval, e.g. "P1M2W"
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setInterval( $value );

	/**
	 * Returns the reason for the end of the subscriptions
	 *
	 * @return integer|null Reason code or NULL for no reason
	 */
	public function getReason();

	/**
	 * Sets the reason for the end of the subscriptions
	 *
	 * @return integer|null Reason code or NULL for no reason
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setReason( $status );

	/**
	 * Returns the status of the subscriptions
	 *
	 * @return integer Subscription status, i.e. "1" for enabled, "0" for disabled
	 */
	public function getStatus();

	/**
	 * Sets the status of the subscriptions
	 *
	 * @return integer Subscription status, i.e. "1" for enabled, "0" for disabled
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setStatus( $status );
}
