<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Gift/present coupon model.
 *
 * @package MShop
 * @subpackage Coupon
 */
class MShop_Coupon_Provider_Present
	extends MShop_Coupon_Provider_Factory_Abstract
	implements MShop_Coupon_Provider_Factory_Interface
{
	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 */
	public function addCoupon( MShop_Order_Item_Base_Interface $base )
	{
		if( $this->_getObject()->isAvailable( $base ) === false ) {
			return;
		}

		$config = $this->getItemBase()->getConfig();

		if( !isset( $config['present.productcode'] ) || !isset( $config['present.quantity'] ) )
		{
			throw new MShop_Coupon_Exception( sprintf(
				'Invalid configuration for coupon provider "%1$s", needs "%2$s"',
				$this->getItemBase()->getProvider(), 'present.productcode, present.quantity'
			) );
		}

		$orderProduct = $this->_createProduct( $config['present.productcode'], $config['present.quantity'] );

		$base->addCoupon( $this->_getCode(), array( $orderProduct ) );
	}
}
