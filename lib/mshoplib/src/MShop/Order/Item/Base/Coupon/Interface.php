<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Order
 */


/**
 * Interface for coupon items.
 *
 * @package MShop
 * @subpackage Order
 */
interface MShop_Order_Item_Base_Coupon_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the base ID of the order.
	 *
	 * @return integer Order base ID.
	 */
	public function getBaseId();

	/**
	 * Sets the base ID of the order.
	 *
	 * @param integer $baseid Order base ID.
	 * @return void
	 */
	public function setBaseId( $baseid );

	/**
	 *	Returns the product id of the ordered product.
	 *
	 *  @return integer Product ID of the ordered product
	 */
	public function getProductId();


	/**
	 * 	Sets the product ID of the ordered product
	 *
	 *	@param integer $productid The product ID of the ordered product
	 * @return void
	 */
	public function setProductId( $productid );

	/**
	 * Returns the coupon code the customer has selected.
	 *
	 * @return string Returns the coupon code the customer has selected.
	 */
	public function getCode();

	/**
	 * Sets the code of a coupon the customer has selected.
	 *
	 * @param string $code The code of a coupon the customer has selected.
	 * @return void
	 */
	public function setCode( $code );

}
