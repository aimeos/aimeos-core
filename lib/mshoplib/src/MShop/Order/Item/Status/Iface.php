<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
interface Iface extends \Aimeos\MShop\Common\Item\Iface
{
	/**
	 * Returns the parentid of the order status.
	 *
	 * @return integer Parent ID of the order status
	 */
	public function getParentId();

	/**
	 * Sets the parentid of the order status.
	 *
	 * @param integer $parentid Parent ID of the order status
	 * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
	 */
	public function setParentId( $parentid );


	/**
	 * Returns the type of the order status.
	 *
	 * @return string Type of the order status
	 */
	public function getType();

	/**
	 * Sets the type of the order status.
	 *
	 * @param string $type Type of the order status
	 * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
	 */
	public function setType( $type );

	/**
	 * Returns the value of the order status.
	 *
	 * @return string Value of the order status
	 */
	public function getValue();

	/**
	 * Sets the value of the order status.
	 *
	 * @param string $value Value of the order status
	 * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
	 */
	public function setValue( $value );
}