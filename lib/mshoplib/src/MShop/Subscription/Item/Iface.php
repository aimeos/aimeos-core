<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
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
	 * @return string|null ID of the base order
	 */
	public function getOrderBaseId() : ?string;

	/**
	 * Sets the ID of the base order item which the customer bought
	 *
	 * @param string $id ID of the base order
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setOrderBaseId( string $id ) : \Aimeos\MShop\Subscription\Item\Iface;

	/**
	 * Returns the ID of the ordered product
	 *
	 * @return string|null ID of the ordered product
	 */
	public function getOrderProductId() : ?string;

	/**
	 * Sets the ID of the ordered product item which the customer subscribed for
	 *
	 * @param string $id ID of the ordered product
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setOrderProductId( string $id ) : \Aimeos\MShop\Subscription\Item\Iface;

	/**
	 * Returns the date of the next subscription renewal
	 *
	 * @return string|null ISO date in "YYYY-MM-DD HH:mm:ss" format
	 */
	public function getDateNext() : ?string;

	/**
	 * Sets the date of the next subscription renewal
	 *
	 * @param string $date ISO date in "YYYY-MM-DD HH:mm:ss" format
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setDateNext( string $date ) : \Aimeos\MShop\Subscription\Item\Iface;

	/**
	 * Returns the date when the subscription renewal ends
	 *
	 * @return string|null ISO date in "YYYY-MM-DD HH:mm:ss" format
	 */
	public function getDateEnd() : ?string;

	/**
	 * Sets the delivery date of the invoice.
	 *
	 * @param string|null $date ISO date in "YYYY-MM-DD HH:mm:ss" format
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setDateEnd( ?string $date ) : \Aimeos\MShop\Subscription\Item\Iface;

	/**
	 * Returns the time interval to pass between the subscription renewals
	 *
	 * @return string PHP time interval, e.g. "P1M2W"
	 */
	public function getInterval() : string;

	/**
	 * Sets the time interval to pass between the subscription renewals
	 *
	 * @param string $value PHP time interval, e.g. "P1M2W"
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setInterval( string $value ) : \Aimeos\MShop\Subscription\Item\Iface;

	/**
	 * Returns the current renewal period of the subscription product
	 *
	 * @return int Current renewal period
	 */
	public function getPeriod() : int;

	/**
	 * Sets the current renewal period of the subscription product
	 *
	 * @param int $value Current renewal period
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setPeriod( int $value ) : \Aimeos\MShop\Subscription\Item\Iface;

	/**
	 * Returns the product ID of the subscription product
	 *
	 * @return string Product ID
	 */
	public function getProductId() : string;

	/**
	 * Sets the product ID of the subscription product
	 *
	 * @param string $value Product ID
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setProductId( string $value ) : \Aimeos\MShop\Subscription\Item\Iface;

	/**
	 * Returns the reason for the end of the subscriptions
	 *
	 * @return int|null Reason code or NULL for no reason
	 */
	public function getReason() : ?int;

	/**
	 * Sets the reason for the end of the subscriptions
	 *
	 * @param int|null Reason code or NULL for no reason
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setReason( ?int $status ) : \Aimeos\MShop\Subscription\Item\Iface;

	/**
	 * Returns the status of the subscriptions
	 *
	 * @return int Subscription status, i.e. "1" for enabled, "0" for disabled
	 */
	public function getStatus() : int;

	/**
	 * Sets the status of the subscriptions
	 *
	 * @param int Subscription status, i.e. "1" for enabled, "0" for disabled
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Subscription\Item\Iface;
}
