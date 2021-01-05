<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Order
 */

namespace Aimeos\MShop\Order\Item\Status;


/**
 * Generic interface for order status.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Parentid\Iface, \Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Returns the value of the order status.
	 *
	 * @return string Value of the order status
	 */
	public function getValue() : string;

	/**
	 * Sets the value of the order status.
	 *
	 * @param string $value Value of the order status
	 * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
	 */
	public function setValue( string $value ) : \Aimeos\MShop\Order\Item\Status\Iface;
}
