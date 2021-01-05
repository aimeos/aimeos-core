<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Negation decorator for service providers
 *
 * This decorator inverts the results of the following decorators or the
 * service provider itself. In combination with the category decorator that
 * enforces a product of a configured category being in the basket, it can be
 * use to disable the service option if such a product is in the basket.
 *
 * @package MShop
 * @subpackage Service
 */
class Not
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	/**
	 * Checks if the products are withing the allowed code is allowed for the service provider.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return bool True if payment provider can be used, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : bool
	{
		return !$this->getProvider()->isAvailable( $basket );
	}
}
