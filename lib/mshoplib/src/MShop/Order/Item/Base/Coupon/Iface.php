<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base\Coupon;


/**
 * Interface for coupon items.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface
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
