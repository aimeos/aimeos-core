<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base\Service\Attribute;


/**
 * Interface for order item base service attribute.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Parentid\Iface
{
	/**
	 * Returns the original attribute ID of the ordered service attribute.
	 *
	 * @return string Attribute ID of the ordered service attribute
	 */
	public function getAttributeId();

	/**
	 * Sets the original attribute ID of the ordered service attribute.
	 *
	 * @param string $id Attribute ID of the ordered service attribute
	 * @return void
	 */
	public function setAttributeId( $id );

	/**
	 * Returns the type of the service attribute item.
	 *
	 * @return string Type of the service attribute item
	 */
	public function getType();

	/**
	 * Sets the type for the service attribute item.
	 *
	 * @param string $type Type as defined by the service provider
	 * @return void
	 */
	public function setType( $type );

	/**
	 * Returns the name of the service attribute item.
	 *
	 * @return string Name of the service attribute item
	 */
	public function getName();

	/**
	 * Sets a new name for the service attribute item.
	 *
	 * @param string $name Name as defined by the service provider
	 * @return void
	 */
	public function setName( $name );

	/**
	 * Returns the code of the service attribute item.
	 *
	 * @return string code of the service attribute item
	 */
	public function getCode();

	/**
	 * Sets a new code for the service attribute item.
	 *
	 * @param string $code Code as defined by the service provider
	 * @return void
	 */
	public function setCode( $code );

	/**
	 * Returns the value of the service attribute item.
	 *
	 * @return string|array Service attribute item value
	 */
	public function getValue();

	/**
	 * Sets a new value for the service attribute item.
	 *
	 * @param string|array $value Service attribute item value
	 * @return void
	 */
	public function setValue( $value );

	/**
	 * Copys all data from a given attribute item.
	 *
	 * @param \Aimeos\MShop\Attribute\Item\Iface $item Attribute item to copy from
	 * @return void
	 */
	public function copyFrom( \Aimeos\MShop\Attribute\Item\Iface $item );
}
