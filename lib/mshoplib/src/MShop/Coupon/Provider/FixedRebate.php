<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Fixed price coupon model.
 *
 * @package MShop
 * @subpackage Coupon
 */
class MShop_Coupon_Provider_FixedRebate
	extends MShop_Coupon_Provider_Abstract
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

		$config = $this->_getItem()->getConfig();

		if( !isset( $config['fixedrebate.productcode'] ) || !isset( $config['fixedrebate.rebate']) )
		{
			throw new MShop_Coupon_Exception( sprintf(
				'Invalid configuration for coupon provider "%1$s", needs "%2$s"',
				$this->_getItem()->getProvider(), 'fixedrebate.productcode, fixedrebate.rebate'
			) );
		}

		$price = MShop_Price_Manager_Factory::createManager( $this->_getContext() )->createItem();
		$price->setValue( -$config['fixedrebate.rebate'] );
		$price->setRebate( $config['fixedrebate.rebate'] );

		$orderProduct = $this->_createProduct( $config['fixedrebate.productcode'], 1 );
		$orderProduct->setPrice( $price );

		$base->addCoupon( $this->_getCode(), array( $orderProduct ) );
	}
}
