<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Free shipping coupon model.
 *
 * @package MShop
 * @subpackage Coupon
 */
class MShop_Coupon_Provider_FreeShipping
	extends MShop_Coupon_Provider_Factory_Base
	implements MShop_Coupon_Provider_Factory_Iface
{
	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Iface $base Basic order of the customer
	 */
	public function addCoupon( MShop_Order_Item_Base_Iface $base )
	{
		if( $this->getObject()->isAvailable( $base ) === false ) {
			return;
		}

		$config = $this->getItemBase()->getConfig();

		if( !isset( $config['freeshipping.productcode'] ) )
		{
			throw new MShop_Coupon_Exception( sprintf(
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