<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
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

	/**
	 * Saves a modified code object to the storage.
	 *
	 * @param \Aimeos\MShop\Coupon\Item\Code\Iface $item Coupon code object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Coupon\Item\Code\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Coupon\Item\Code\Iface;
}
