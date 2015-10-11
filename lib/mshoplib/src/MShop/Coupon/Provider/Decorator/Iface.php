<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


/**
 * Coupon decorator interface.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface Iface
	extends \Aimeos\MShop\Coupon\Provider\Iface
{
	/**
	 * Initializes the coupon provider.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param \Aimeos\MShop\Coupon\Item\Iface $item Coupon item to set
	 * @param string $code Coupon code entered by the customer
	 * @param \Aimeos\MShop\Coupon\Provider\Iface $provider Coupon provider
	 * @return void
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Coupon\Item\Iface $item,
		$code, \Aimeos\MShop\Coupon\Provider\Iface $provider );
}