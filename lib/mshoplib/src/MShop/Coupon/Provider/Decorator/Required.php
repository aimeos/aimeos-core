<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Required decorator for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
class MShop_Coupon_Provider_Decorator_Required
	extends MShop_Coupon_Provider_Decorator_Abstract
	implements MShop_Coupon_Provider_Decorator_Interface
{
	/**
	 * Checks for requirements.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 * @return boolean True if the requirements are met, false if not
	 */
	public function isAvailable( MShop_Order_Item_Base_Interface $base )
	{
		if( ( $prodcode = $this->_getConfigValue( 'required.productcode' ) ) !== null )
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
