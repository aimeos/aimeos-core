<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH
 * @package MShop
 * @subpackage Coupon
 * @version $Id: PercentRebate.php 37 2012-08-08 17:37:40Z fblasel $
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

		if( $this->_checkConstraints( $base, $config ) === true )
		{
			if( !isset( $config['product'] ) || !isset( $config['rebate'] ) ) {
				throw new MShop_Coupon_Exception( 'Invalid configuration for percent rebate, "product" and "rebate" required.');
			}

			$sum = 0.00;
			foreach( $base->getProducts() AS $prod ) {
				$sum += $prod->getPrice()->getValue() * $prod->getQuantity();
			}

			$rebate = round( $sum * (float) $config['rebate'] / 100, 2 );
			$price = MShop_Price_Manager_Factory::createManager( $this->_getContext() )->createItem();
			$price->setValue( -$rebate );
			$price->setRebate( $rebate );

			$orderProduct = $this->_createProduct( $config['product'], 1 );
			$orderProduct->setPrice( $price );

			$coupons[] = $orderProduct;
		}

		$base->addCoupon( $this->_getCode(), $coupons );
	}
}
