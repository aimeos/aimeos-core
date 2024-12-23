<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2024
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


/**
 * Negation decorator for coupon providers
 *
 * @package MShop
 * @subpackage Coupon
 */
class Not
	extends \Aimeos\MShop\Coupon\Provider\Decorator\Base
	implements \Aimeos\MShop\Coupon\Provider\Decorator\Iface
{
	/**
	 * Tests if a coupon should be granted
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Basket object
	 * @return bool True if available, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Iface $order ) : bool
	{
		return !$this->getProvider()->isAvailable( $order );
	}
}
