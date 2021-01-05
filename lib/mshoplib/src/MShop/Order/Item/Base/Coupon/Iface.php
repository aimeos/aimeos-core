<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	 * @return string|null Order base ID.
	 */
	public function getBaseId() : ?string;

	/**
	 * Sets the base ID of the order.
	 *
	 * @param string|null $baseid Order base ID.
	 * @return \Aimeos\MShop\Order\Item\Base\Coupon\Iface Order base coupon item for chaining method calls
	 */
	public function setBaseId( ?string $baseid ) : \Aimeos\MShop\Order\Item\Base\Coupon\Iface;

	/**
	 *	Returns the product id of the ordered product.
	 *
	 *  @return string|null Product ID of the ordered product
	 */
	public function getProductId() : ?string;


	/**
	 * 	Sets the product ID of the ordered product
	 *
	 *	@param string $productid The product ID of the ordered product
	 * @return \Aimeos\MShop\Order\Item\Base\Coupon\Iface Order base coupon item for chaining method calls
	 */
	public function setProductId( string $productid ) : \Aimeos\MShop\Order\Item\Base\Coupon\Iface;

	/**
	 * Returns the coupon code the customer has selected.
	 *
	 * @return string|null Returns the coupon code the customer has selected.
	 */
	public function getCode() : ?string;

	/**
	 * Sets the code of a coupon the customer has selected.
	 *
	 * @param string $code The code of a coupon the customer has selected.
	 * @return \Aimeos\MShop\Order\Item\Base\Coupon\Iface Order base coupon item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Order\Item\Base\Coupon\Iface;
}
