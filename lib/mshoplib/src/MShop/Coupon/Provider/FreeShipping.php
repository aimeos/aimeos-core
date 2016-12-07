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
 * Free shipping coupon model.
 *
 * @package MShop
 * @subpackage Coupon
 */
class FreeShipping
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
		if( $this->getObject()->isAvailable( $base ) === false ) {
			return;
		}

		$config = $this->getItemBase()->getConfig();

		if( !isset( $config['freeshipping.productcode'] ) )
		{
			throw new \Aimeos\MShop\Coupon\Exception( sprintf(
				'Invalid configuration for coupon provider "%1$s", needs "%2$s"',
				$this->getItemBase()->getProvider(), 'freeshipping.productcode'
			) );
		}

		$price = clone ( $base->getService( 'delivery' )->getPrice() );
		$price->setRebate( $price->getCosts() );
		$price->setCosts( -$price->getCosts() );

		$orderProduct = $this->createProduct( $config['freeshipping.productcode'], 1 );
		$orderProduct->setPrice( $price );

		$base->addCoupon( $this->getCode(), array( $orderProduct ) );
	}
}