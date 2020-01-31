<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


/**
 * Example decorator for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
class Example
	extends \Aimeos\MShop\Coupon\Provider\Decorator\Base
	implements \Aimeos\MShop\Coupon\Provider\Decorator\Iface
{
	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Provider object for method chaining
	 */
	public function update( \Aimeos\MShop\Order\Item\Base\Iface $base ) : \Aimeos\MShop\Coupon\Provider\Iface
	{
		$this->getProvider()->update( $base );
		return $this;
	}


	/**
	 * Tests if a coupon should be granted
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base
	 * @return bool True if available, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base ) : bool
	{
		return true;
	}
}
