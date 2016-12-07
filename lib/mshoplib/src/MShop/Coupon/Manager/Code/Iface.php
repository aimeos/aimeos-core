<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	extends \Aimeos\MShop\Common\Manager\Factory\Iface, \Aimeos\MShop\Common\Manager\Find\Iface
{
	/**
	 * Decreases the counter of the coupon code.
	 *
	 * @param string $couponCode Unique code of a coupon
	 * @param integer $amount Amount the coupon count should be decreased
	 * @return void
	 */
	public function decrease( $couponCode, $amount );


	/**
	 * Increases the counter of the coupon code.
	 *
	 * @param string $couponCode Unique code of a coupon
	 * @param integer $amount Amount the coupon count should be increased
	 * @return void
	 */
	public function increase( $couponCode, $amount );
}
