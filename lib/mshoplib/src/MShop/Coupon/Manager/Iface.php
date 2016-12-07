<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Manager;


/**
 * Generic coupon manager interface for creating and handling coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/**
	 * Returns the coupon model which belongs to the given code.
	 *
	 * @param \Aimeos\MShop\Coupon\Item\Iface $item Coupon item
	 * @param string $code Coupon code
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Coupon model
	 * @throws \Aimeos\MShop\Coupon\Exception If coupon model couldn't be found
	 */
	public function getProvider( \Aimeos\MShop\Coupon\Item\Iface $item, $code );
}
