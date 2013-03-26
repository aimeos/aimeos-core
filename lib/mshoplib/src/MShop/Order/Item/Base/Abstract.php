<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 * @version $Id: Abstract.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Abstract order base class with necessary constants and basic methods.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class MShop_Order_Item_Base_Abstract
	extends MW_Observer_Publisher_Abstract
	implements MShop_Order_Item_Base_Interface
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
	 * @throws MShop_Order_Exception If parts constant is invalid
	 */
	protected function _checkParts($value)
	{
		$value = (int) $value;

		if( $value < MShop_Order_Item_Base_Abstract::PARTS_NONE || $value > MShop_Order_Item_Base_Abstract::PARTS_ALL ) {
			throw new MShop_Order_Exception( sprintf( 'An error occured in the order. Flags "%1$s" not within allowed range.', $value ) );
		}
	}


	/**
	 * Checks if a order product contains all required values.
	 *
	 * @param MShop_Order_Item_Base_Product_Interface $item Order product item
	 * @throws MShop_Exception if the price item or product code is missing
	 */
	protected function _checkProduct( MShop_Order_Item_Base_Product_Interface $item )
	{
		if( $item->getProductCode() === '' ) {
			throw new MShop_Order_Exception( sprintf( 'Item needs a product code' ) );
		}
	}
}
