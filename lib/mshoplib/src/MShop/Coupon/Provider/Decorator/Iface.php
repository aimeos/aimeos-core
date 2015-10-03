<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Coupon decorator interface.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface MShop_Coupon_Provider_Decorator_Iface
	extends MShop_Coupon_Provider_Iface
{
	/**
	 * Initializes the coupon provider.
	 *
	 * @param MShop_Context_Item_Iface $context Context object
	 * @param MShop_Coupon_Item_Iface $item Coupon item to set
	 * @param string $code Coupon code entered by the customer
	 * @param MShop_Coupon_Provider_Iface $provider Coupon provider
	 * @return void
	 */
	public function __construct( MShop_Context_Item_Iface $context, MShop_Coupon_Item_Iface $item,
		$code, MShop_Coupon_Provider_Iface $provider );
}