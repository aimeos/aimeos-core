<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider;


/**
 * Example model for coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
class Example
	extends \Aimeos\MShop\Coupon\Provider\Factory\Base
	implements \Aimeos\MShop\Coupon\Provider\Factory\Iface
{
	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 */
	public function addCoupon( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
	}


	/**
	 * Removes the result of a coupon from the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 */
	public function deleteCoupon( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
	}


	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 */
	public function updateCoupon( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
	}


	/**
	 * Tests if a coupon should be granted
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		return true;
	}
}
