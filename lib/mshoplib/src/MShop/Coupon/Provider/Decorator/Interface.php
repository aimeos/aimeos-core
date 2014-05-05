<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Coupon decorator interface.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface MShop_Coupon_Provider_Decorator_Interface
	extends MShop_Coupon_Provider_Interface
{
	/**
	 * Initializes the coupon provider.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param MShop_Coupon_Item_Interface $item Coupon item to set
	 * @param string $code Coupon code entered by the customer
	 * @param MShop_Coupon_Provider_Interface $provider Coupon provider
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Coupon_Item_Interface $item,
		$code, MShop_Coupon_Provider_Interface $provider );
}