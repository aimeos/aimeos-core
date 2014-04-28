<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Coupon
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
