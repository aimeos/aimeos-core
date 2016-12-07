<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


/**
 * Required decorator for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
class Required
	extends \Aimeos\MShop\Coupon\Provider\Decorator\Base
	implements \Aimeos\MShop\Coupon\Provider\Decorator\Iface
{
	/**
	 * Checks for requirements.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return boolean True if the requirements are met, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		if( ( $prodcode = $this->getConfigValue( 'required.productcode' ) ) !== null )
		{
			foreach( $base->getProducts() as $product )
			{
				if( $product->getProductCode() == $prodcode ) {
					return parent::isAvailable( $base );
				}
			}

			return false;
		}

		return true;
	}
}
