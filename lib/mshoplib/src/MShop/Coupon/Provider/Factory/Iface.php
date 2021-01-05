<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider\Factory;


/**
 * Factory interface for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface Iface
{
	/**
	 * Initializes the coupon model.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param \Aimeos\MShop\Coupon\Item\Iface $item Coupon item to set
	 * @param string $code Coupon code entered by the customer
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Coupon\Item\Iface $item, string $code );
}
