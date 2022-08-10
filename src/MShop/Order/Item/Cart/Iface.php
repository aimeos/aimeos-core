<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
 * @package MShop
 * @subpackage Order
 */

namespace Aimeos\MShop\Order\Item\Cart;


/**
 * Generic interface for carts.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface
{
	/**
	 * Returns the content of the cart.
	 *
	 * @return string Content of the cart
	 */
	public function getContent() : string;

	/**
	 * Sets the content of the cart.
	 *
	 * @param string $value Content of the cart
	 * @return \Aimeos\MShop\Order\Item\Cart\Iface Cart item for chaining method calls
	 */
	public function setContent( string $value ) : \Aimeos\MShop\Order\Item\Cart\Iface;

	/**
	 * Returns the ID of the customer who owns the cart.
	 *
	 * @return string Unique ID of the customer
	 */
	public function getCustomerId() : string;

	/**
	 * Sets the ID of the customer who owned the cart.
	 *
	 * @param string $customerid Unique ID of the customer
	 * @return \Aimeos\MShop\Order\Item\Cart\Iface Cart item for chaining method calls
	 */
	public function setCustomerId( ?string $customerid ) : \Aimeos\MShop\Order\Item\Cart\Iface;

	/**
	 * Returns the name of the cart.
	 *
	 * @return string Name for the cart
	 */
	public function getName() : string;

	/**
	 * Sets the name of the cart.
	 *
	 * @param string $name Name for the cart
	 * @return \Aimeos\MShop\Order\Item\Cart\Iface Cart item for chaining method calls
	 */
	public function setName( ?string $name ) : \Aimeos\MShop\Order\Item\Cart\Iface;
}
