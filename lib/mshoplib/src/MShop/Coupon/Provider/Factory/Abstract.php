<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Base class for coupon provider
 *
 * @package MShop
 * @subpackage Coupon
 */
abstract class MShop_Coupon_Provider_Factory_Abstract
	extends MShop_Coupon_Provider_Abstract
{
	/**
	 * Initializes the object instance
	 *
	 * PHP 7 fails with a wierd fatal error that decorator constructors must be
	 * compatible with the constructor of the factory interface if this
	 * intermediate constructor isn't implemented!
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param MShop_Coupon_Item_Interface $item Coupon item
	 * @param string $code Coupon code entered by the customer
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Coupon_Item_Interface $item, $code )
	{
		parent::__construct( $context, $item, $code );
	}
}
