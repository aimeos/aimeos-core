<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH
 * @package MShop
 * @subpackage Coupon
 * @version $Id: Interface.php 37 2012-08-08 17:37:40Z fblasel $
 */


/**
 * Generic coupon manager interface for creating and handling coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface MShop_Coupon_Manager_Code_Interface
	extends MShop_Common_Manager_Factory_Interface
{
	/**
	 * Decreases the count of the coupon code.
	 *
	 * @param string $couponCode Unique code of a coupon
	 * @param integer $amount Amount the coupon count should be decreased
	 */
	public function decrease( $couponCode, $amount = 1 );
}
