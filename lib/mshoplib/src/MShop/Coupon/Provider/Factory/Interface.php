<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Factory interface for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface MShop_Coupon_Provider_Factory_Interface
	extends MShop_Coupon_Provider_Interface
{
	/**
	 * Initializes the coupon model.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param MShop_Coupon_Item_Interface $item Coupon item to set
	 * @param string $code Coupon code entered by the customer
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Coupon_Item_Interface $item, $code, &$outer );
}
