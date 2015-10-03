<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Generic interface for coupons models implementing the coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface MShop_Coupon_Provider_Iface
{
	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Iface $base Basic order of the customer
	 * @return void
	 */
	public function addCoupon( MShop_Order_Item_Base_Iface $base );

	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Iface $base Basic order of the customer
	 * @return void
	 */
	public function updateCoupon( MShop_Order_Item_Base_Iface $base );

	/**
	 * Removes the result of a coupon from the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Iface $base Basic order of the customer
	 * @return void
	 */
	public function deleteCoupon( MShop_Order_Item_Base_Iface $base );

	/**
	 * Tests if a coupon should be granted.
	 *
	 * @param MShop_Order_Item_Base_Iface $base Basic order of the customer
	 * @return boolean True of coupon can be granted, false if not
	 */
	public function isAvailable( MShop_Order_Item_Base_Iface $base );
}
