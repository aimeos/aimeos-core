<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH
 * @package MShop
 * @subpackage Coupon
 * @version $Id: Interface.php 37 2012-08-08 17:37:40Z fblasel $
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
		$code, MShop_Coupon_Provider_Interface $provider, &$outer );
}