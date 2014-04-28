<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH
 * @package MShop
 * @subpackage Coupon
 * @version $Id: BasketValues.php 37 2012-08-08 17:37:40Z fblasel $
 */


/**
 * BasketValues decorator for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
class MShop_Coupon_Provider_Decorator_BasketValues
	extends MShop_Coupon_Provider_Decorator_Abstract
{
	/**
	 * Checks for the min/max order value.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 * @param array $config Associative array with config content
	 * @return boolean True if the basket matches the constraints, false if not
	 */
	public function isAvailable( MShop_Order_Item_Base_Interface $base )
	{
		$config = $this->_getItem()->getConfig();

		$currency = $base->getPrice()->getCurrencyId();
		$price = $base->getPrice();
		$value = $price->getValue() + $price->getRebate();

		if ( isset( $config['basketvalues.total-value-min'][ $currency ] ) &&
			$config['basketvalues.total-value-min'][ $currency ] > $value ) {
			return false;
		}

		if ( isset( $config['basketvalues.total-value-max'][ $currency ] ) &&
			$config['basketvalues.total-value-max'][ $currency ] < $value ) {
			return false;
		}

		return parent::isAvailable( $base );
	}

}
