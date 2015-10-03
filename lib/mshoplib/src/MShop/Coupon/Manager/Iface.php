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
interface MShop_Coupon_Manager_Iface
	extends MShop_Common_Manager_Factory_Iface
{
	/**
	 * Returns the coupon model which belongs to the given code.
	 *
	 * @param MShop_Coupon_Item_Iface $item Coupon item
	 * @param string $code Coupon code
	 * @return MShop_Coupon_Provider_Iface Coupon model
	 * @throws MShop_Coupon_Exception If coupon model couldn't be found
	 */
	public function getProvider( MShop_Coupon_Item_Iface $item, $code );
}
