<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Coupon
 */


/**
 * BasketValues decorator for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
class MShop_Coupon_Provider_Decorator_BasketValues
	extends MShop_Coupon_Provider_Decorator_Abstract
	implements MShop_Coupon_Provider_Decorator_Interface
{
	/**
	 * Checks for the min/max order value.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 * @return boolean True if the basket matches the constraints, false if not
	 */
	public function isAvailable( MShop_Order_Item_Base_Interface $base )
	{
		$price = $base->getPrice();
		$currency = $price->getCurrencyId();
		$value = $price->getValue() + $price->getRebate();

		$minvalue = $this->_getConfigValue( 'basketvalues.total-value-min', array() );

		if( isset( $minvalue[$currency] ) && $minvalue[$currency] > $value ) {
			return false;
		}

		$maxvalue = $this->_getConfigValue( 'basketvalues.total-value-max', array() );

		if( isset( $maxvalue[$currency] ) && $maxvalue[$currency] < $value ) {
			return false;
		}

		return parent::isAvailable( $base );
	}
}
