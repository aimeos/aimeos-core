<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Generic coupon manager interface for creating and handling coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface MShop_Coupon_Manager_Code_Iface
	extends MShop_Common_Manager_Factory_Iface
{
	/**
	 * Decreases the counter of the coupon code.
	 *
	 * @param string $couponCode Unique code of a coupon
	 * @param integer $amount Amount the coupon count should be decreased
	 * @return void
	 */
	public function decrease( $couponCode, $amount );


	/**
	 * Increases the counter of the coupon code.
	 *
	 * @param string $couponCode Unique code of a coupon
	 * @param integer $amount Amount the coupon count should be increased
	 * @return void
	 */
	public function increase( $couponCode, $amount );
}
