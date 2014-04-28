<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH
 * @package MShop
 * @subpackage Coupon
 * @version $Id: FreeShipping.php 37 2012-08-08 17:37:40Z fblasel $
 */


/**
 * Free shipping coupon model.
 *
 * @package MShop
 * @subpackage Coupon
 */
class MShop_Coupon_Provider_FreeShipping extends MShop_Coupon_Provider_Abstract
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
			if( !isset( $config['product'] ) ) {
				throw new MShop_Coupon_Exception( 'Invalid configuration for free shipping, "product" required!' );
			}

			$price = clone ( $base->getService('delivery')->getPrice() );
			$price->setRebate( $price->getCosts() );
			$price->setCosts( -$price->getCosts() );

			$orderProduct = $this->_createProduct( $config['product'], 1 );
			$orderProduct->setPrice( $price );

			$coupons[] = $orderProduct;
		}

		$base->addCoupon( $this->_getCode(), $coupons );
	}
}