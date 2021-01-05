<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Decorator for service providers setting costs to zero.
 *
 * @package MShop
 * @subpackage Service
 */
class Nocosts
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	/**
	 * Returns the costs per item as negative value to get no costs at all.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return \Aimeos\MShop\Price\Item\Iface Price item containing the price, shipping, rebate
	 */
	public function calcPrice( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : \Aimeos\MShop\Price\Item\Iface
	{
		$costs = 0;
		$price = $this->getProvider()->calcPrice( $basket );

		foreach( $basket->getProducts() as $product ) {
			$costs += $product->getPrice()->getCosts() * $product->getQuantity();
		}

		return $price->setCosts( -$costs );
	}
}
