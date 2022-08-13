<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
 * @package MShop
 * @subpackage Order
 */

namespace Aimeos\MShop\Order\Item\Basket;


/**
 * Generic interface for baskets.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface
{
	/**
	 * Returns the content of the basket.
	 *
	 * @return string Content of the basket
	 */
	public function getContent() : string;

	/**
	 * Sets the content of the basket.
	 *
	 * @param string $value Content of the basket
	 * @return \Aimeos\MShop\Order\Item\Basket\Iface Basket item for chaining method calls
	 */
	public function setContent( string $value ) : \Aimeos\MShop\Order\Item\Basket\Iface;

	/**
	 * Returns the ID of the customer who owns the basket.
	 *
	 * @return string Unique ID of the customer
	 */
	public function getCustomerId() : string;

	/**
	 * Sets the ID of the customer who owned the basket.
	 *
	 * @param string $customerid Unique ID of the customer
	 * @return \Aimeos\MShop\Order\Item\Basket\Iface Basket item for chaining method calls
	 */
	public function setCustomerId( ?string $customerid ) : \Aimeos\MShop\Order\Item\Basket\Iface;

	/**
	 * Returns the name of the basket.
	 *
	 * @return string Name for the basket
	 */
	public function getName() : string;

	/**
	 * Sets the name of the basket.
	 *
	 * @param string $name Name for the basket
	 * @return \Aimeos\MShop\Order\Item\Basket\Iface Basket item for chaining method calls
	 */
	public function setName( ?string $name ) : \Aimeos\MShop\Order\Item\Basket\Iface;
}
