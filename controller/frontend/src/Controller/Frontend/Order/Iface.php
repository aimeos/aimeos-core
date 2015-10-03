<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Frontend
 */


/**
 * Interface for order frontend controllers.
 *
 * @package Controller
 * @subpackage Frontend
 */
interface Controller_Frontend_Order_Iface
	extends Controller_Frontend_Common_Iface
{
	/**
	 * Creates a new order from the given basket.
	 *
	 * Saves the given basket to the storage including the addresses, coupons,
	 * products, services, etc. and creates/stores a new order item for that
	 * order.
	 *
	 * @param MShop_Order_Item_Base_Iface $basket Basket object to be stored
	 * @return MShop_Order_Item_Iface Order item that belongs to the stored basket
	 */
	public function store( MShop_Order_Item_Base_Iface $basket );


	/**
	 * Blocks the resources listed in the order.
	 *
	 * Every order contains resources like products or redeemed coupon codes
	 * that must be blocked so they can't be used by another customer in a
	 * later order. This method reduces the the stock level of products, the
	 * counts of coupon codes and others.
	 *
	 * It's save to call this method multiple times for one order. In this case,
	 * the actions will be executed only once. All subsequent calls will do
	 * nothing as long as the resources haven't been unblocked in the meantime.
	 *
	 * You can also block and unblock resources several times. Please keep in
	 * mind that unblocked resources may be reused by other orders in the
	 * meantime. This can lead to an oversell of products!
	 *
	 * @param MShop_Order_Item_Iface $orderItem Order item object
	 * @return void
	 */
	public function block( MShop_Order_Item_Iface $orderItem );


	/**
	 * Frees the resources listed in the order.
	 *
	 * If customers created orders but didn't pay for them, the blocked resources
	 * like products and redeemed coupon codes must be unblocked so they can be
	 * ordered again or used by other customers. This method increased the stock
	 * level of products, the counts of coupon codes and others.
	 *
	 * It's save to call this method multiple times for one order. In this case,
	 * the actions will be executed only once. All subsequent calls will do
	 * nothing as long as the resources haven't been blocked in the meantime.
	 *
	 * You can also unblock and block resources several times. Please keep in
	 * mind that unblocked resources may be reused by other orders in the
	 * meantime. This can lead to an oversell of products!
	 *
	 * @param MShop_Order_Item_Iface $orderItem Order item object
	 * @return void
	 */
	public function unblock( MShop_Order_Item_Iface $orderItem );


	/**
	 * Blocks or frees the resources listed in the order if necessary.
	 *
	 * After payment status updates, the resources like products or coupon
	 * codes listed in the order must be blocked or unblocked. This method
	 * cares about executing the appropriate action depending on the payment
	 * status.
	 *
	 * It's save to call this method multiple times for one order. In this case,
	 * the actions will be executed only once. All subsequent calls will do
	 * nothing as long as the payment status hasn't changed in the meantime.
	 *
	 * @param MShop_Order_Item_Iface $orderItem Order item object
	 * @return void
	 */
	public function update( MShop_Order_Item_Iface $orderItem );
}
