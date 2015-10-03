<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Example model for coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
class MShop_Coupon_Provider_Example
	extends MShop_Coupon_Provider_Factory_Base
	implements MShop_Coupon_Provider_Factory_Iface
{
	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Iface $base Basic order of the customer
	 */
	public function addCoupon( MShop_Order_Item_Base_Iface $base )
	{
	}


	/**
	 * Removes the result of a coupon from the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Iface $base Basic order of the customer
	 */
	public function deleteCoupon( MShop_Order_Item_Base_Iface $base )
	{
	}


	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Iface $base Basic order of the customer
	 */
	public function updateCoupon( MShop_Order_Item_Base_Iface $base )
	{
	}


	/**
	 * Tests if a coupon should be granted
	 *
	 * @param MShop_Order_Item_Base_Iface $base
	 */
	public function isAvailable( MShop_Order_Item_Base_Iface $base )
	{
		return true;
	}
}
