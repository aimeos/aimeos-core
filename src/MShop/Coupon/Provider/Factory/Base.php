<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider\Factory;


/**
 * Base class for coupon provider
 *
 * @package MShop
 * @subpackage Coupon
 */
abstract class Base
	extends \Aimeos\MShop\Coupon\Provider\Base
{
	/**
	 * Initializes the object instance
	 *
	 * PHP 7 fails with a wierd fatal error that decorator constructors must be
	 * compatible with the constructor of the factory interface if this
	 * intermediate constructor isn't implemented!
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @param \Aimeos\MShop\Coupon\Item\Iface $item Coupon item
	 * @param string $code Coupon code entered by the customer
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context, \Aimeos\MShop\Coupon\Item\Iface $item, string $code )
	{
		parent::__construct( $context, $item, $code );
	}
}
