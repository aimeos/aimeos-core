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
class MShop_Coupon_Provider_FixedRebate extends MShop_Coupon_Provider_Abstract
{
	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 */
	public function addCoupon( MShop_Order_Item_Base_Interface $base )
	{
		$config = $this->_getItem()->getConfig();

		$coupons = array();

		if( ( $this->_checkConstraints( $base, $config ) === true ) && ( $this->_getOuterObject()->isAvailable( $base ) ) )
		{
			if( !isset( $config['product'] ) || !isset( $config['rebate']) ) {
				throw new MShop_Coupon_Exception( 'Invalid configuration for fixed rebate, need "product" and "rebate"' );
			}

			$price = MShop_Price_Manager_Factory::createManager( $this->_getContext() )->createItem();
			$price->setValue( -$config['rebate'] );
			$price->setRebate( $config['rebate'] );

			$orderProduct = $this->_createProduct( $config['product'], 1 );
			$orderProduct->setPrice( $price );

			$coupons[] = $orderProduct;
		}

		$base->addCoupon( $this->_getCode(), $coupons );
	}
}
