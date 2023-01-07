<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Service\Transaction;


/**
 * Interface for order service transaction item
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Config\Iface,
		\Aimeos\MShop\Common\Item\Parentid\Iface, \Aimeos\MShop\Common\Item\Status\Iface,
		\Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Sets the site ID of the item.
	 *
	 * @param string $value Unique site ID of the item
	 * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface Order base service attribute item for chaining method calls
	 */
	public function setSiteId( string $value ) : \Aimeos\MShop\Order\Item\Service\Transaction\Iface;

	/**
	 * Returns the price object which belongs to the service item.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface Price item
	 */
	public function getPrice() : \Aimeos\MShop\Price\Item\Iface;

	/**
	 * Sets a new price object for the service item.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
	 */
	public function setPrice( \Aimeos\MShop\Price\Item\Iface $price ) : \Aimeos\MShop\Order\Item\Service\Transaction\Iface;
}
