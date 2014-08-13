<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Generic interface for coupons models implementing the coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface MShop_Coupon_Provider_Interface
{
	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 * @return void
	 */
	public function addCoupon( MShop_Order_Item_Base_Interface $base );

	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 * @return void
	 */
	public function updateCoupon( MShop_Order_Item_Base_Interface $base );

	/**
	 * Removes the result of a coupon from the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 * @return void
	 */
	public function deleteCoupon( MShop_Order_Item_Base_Interface $base );

	/**
	 * Tests if a coupon should be granted.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 * @return bool
	 */
	public function isAvailable( MShop_Order_Item_Base_Interface $base );
}
