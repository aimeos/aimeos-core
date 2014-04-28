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
interface MShop_Coupon_Manager_Interface
	extends MShop_Common_Manager_Factory_Interface
{
	/**
	 * Returns the coupon model which belongs to the given code.
	 *
	 * @param MShop_Coupon_Item_Interface $item Coupon item
	 * @param string $code Coupon code
	 * @return MShop_Coupon_Provider_Interface Coupon model
	 * @throws MShop_Coupon_Exception If coupon model couldn't be found
	 */
	public function getProvider( MShop_Coupon_Item_Interface $item, $code );
}
