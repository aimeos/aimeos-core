<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


/**
 * BasketValues decorator for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
class BasketValues
	extends \Aimeos\MShop\Coupon\Provider\Decorator\Base
	implements \Aimeos\MShop\Coupon\Provider\Decorator\Iface
{
	/**
	 * Checks for the min/max order value.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return boolean True if the basket matches the constraints, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		$price = $base->getPrice();
		$currency = $price->getCurrencyId();
		$value = $price->getValue() + $price->getRebate();

		$minvalue = $this->getConfigValue( 'basketvalues.total-value-min', [] );

		if( isset( $minvalue[$currency] ) && $minvalue[$currency] > $value ) {
			return false;
		}

		$maxvalue = $this->getConfigValue( 'basketvalues.total-value-max', [] );

		if( isset( $maxvalue[$currency] ) && $maxvalue[$currency] < $value ) {
			return false;
		}

		return parent::isAvailable( $base );
	}
}
