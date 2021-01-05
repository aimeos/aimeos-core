<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Manager\Code;


/**
 * Generic coupon manager interface for creating and handling coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MShop\Common\Manager\Find\Iface
{
	/**
	 * Decreases the counter of the coupon code.
	 *
	 * @param string $couponCode Unique code of a coupon
	 * @param int $amount Amount the coupon count should be decreased
	 * @return \Aimeos\MShop\Coupon\Manager\Code\Iface Manager object for chaining method calls
	 */
	public function decrease( string $couponCode, int $amount ) : \Aimeos\MShop\Coupon\Manager\Code\Iface;

	/**
	 * Increases the counter of the coupon code.
	 *
	 * @param string $couponCode Unique code of a coupon
	 * @param int $amount Amount the coupon count should be increased
	 * @return \Aimeos\MShop\Coupon\Manager\Code\Iface Manager object for chaining method calls
	 */
	public function increase( string $couponCode, int $amount ) : \Aimeos\MShop\Coupon\Manager\Code\Iface;
}
