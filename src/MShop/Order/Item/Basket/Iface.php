<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
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
	 * Returns the basket object.
	 *
	 * @return \Aimeos\MShop\Order\Item\Iface|null $basket Basket object
	 */
	public function getItem() : ?\Aimeos\MShop\Order\Item\Iface;

	/**
	 * Sets the basket object.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
	 * @return \Aimeos\MShop\Order\Item\Basket\Iface Basket item for chaining method calls
	 */
	public function setItem( \Aimeos\MShop\Order\Item\Iface $basket ) : \Aimeos\MShop\Order\Item\Basket\Iface;

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
