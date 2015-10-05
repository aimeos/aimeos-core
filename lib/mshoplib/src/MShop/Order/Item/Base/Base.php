<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base;


/**
 * Abstract order base class with necessary constants and basic methods.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class Base
	extends \Aimeos\MW\Observer\Publisher\Base
	implements \Aimeos\MShop\Order\Item\Base\Iface
{
	/**
	 * Check no basket content.
	 * Don't check if the basket is ready for checkout or ordering.
	 */
	const PARTS_NONE = 0;

	/**
	 * Check basket for products.
	 * Checks if the basket complies to the product related requirements.
	 */
	const PARTS_PRODUCT = 1;

	/**
	 * Check basket for addresses.
	 * Checks if the basket complies to the address related requirements.
	 */
	const PARTS_ADDRESS = 2;

	/**
	 * Check basket for delivery/payment.
	 * Checks if the basket complies to the delivery/payment related
	 * requirements.
	 */
	const PARTS_SERVICE = 4;

	/**
	 * Check basket for all parts.
	 * This constant matches all other part constants.
	 */
	const PARTS_ALL = 7;


	/**
	 * Checks the constants for the different parts of the basket.
	 *
	 * @param integer $value Part constant
	 * @throws \Aimeos\MShop\Order\Exception If parts constant is invalid
	 */
	protected function checkParts( $value )
	{
		$value = (int) $value;

		if( $value < \Aimeos\MShop\Order\Item\Base\Base::PARTS_NONE || $value > \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Flags "%1$s" not within allowed range', $value ) );
		}
	}


	/**
	 * Checks if a order product contains all required values.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $item Order product item
	 * @throws \Aimeos\MShop\Exception if the price item or product code is missing
	 */
	protected function checkProduct( \Aimeos\MShop\Order\Item\Base\Product\Iface $item )
	{
		if( $item->getProductCode() === '' ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Product does not contain all required values. Product code for item not available.' ) );
		}
	}
}
