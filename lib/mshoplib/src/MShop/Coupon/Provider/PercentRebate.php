<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Percentage price coupon model.
 *
 * @package MShop
 * @subpackage Coupon
 */
class MShop_Coupon_Provider_PercentRebate extends MShop_Coupon_Provider_Abstract
{
	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 */
	public function addCoupon( MShop_Order_Item_Base_Interface $base )
	{
		$coupons = array();
		$config = $this->_getItem()->getConfig();

		if( !isset( $config['percentrebate.productcode'] ) || !isset( $config['percentrebate.rebate']) )
		{
			throw new MShop_Coupon_Exception( sprintf(
				'Invalid configuration for coupon provider "%1$s", needs "%2$s"',
				$this->_getItem()->getProvider(), 'percentrebate.productcode, percentrebate.rebate'
			) );
		}

			$sum = 0.00;
			foreach( $base->getProducts() AS $prod ) {
				$sum += $prod->getPrice()->getValue() * $prod->getQuantity();
			}

			$rebate = round( $sum * (float) $config['rebate'] / 100, 2 );
			$price = MShop_Price_Manager_Factory::createManager( $this->_getContext() )->createItem();
			$price->setValue( -$rebate );
			$price->setRebate( $rebate );

		$rebate = round( $sum * (float) $config['percentrebate.rebate'] / 100, 2 );
		$price = MShop_Price_Manager_Factory::createManager( $this->_getContext() )->createItem();
		$price->setValue( -$rebate );
		$price->setRebate( $rebate );

		$orderProduct = $this->_createProduct( $config['percentrebate.productcode'], 1 );
		$orderProduct->setPrice( $price );

		$base->addCoupon( $this->_getCode(), $coupons );
	}
}
