<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Example model for coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
class MShop_Coupon_Provider_Example extends MShop_Coupon_Provider_Abstract
{
	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 */
	public function addCoupon( MShop_Order_Item_Base_Interface $base )
	{
	}


	/**
	 * Removes the result of a coupon from the order base instance.
	 *
	 * @param MShop_Order_Base_Interface $base Basic order of the customer
	 */
	public function deleteCoupon( MShop_Order_Item_Base_Interface $base )
	{
	}


	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 */
	public function updateCoupon( MShop_Order_Item_Base_Interface $base )
	{
	}
}
