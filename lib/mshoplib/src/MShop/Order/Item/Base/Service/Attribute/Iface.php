<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Parentid\Iface, \Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Sets the site ID of the item.
	 *
	 * @param string $value Unique site ID of the item
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setSiteId( string $value ) : \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface;

	/**
	 * Returns the original attribute ID of the ordered service attribute.
	 *
	 * @return string Attribute ID of the ordered service attribute
	 */
	public function getAttributeId() : string;

	/**
	 * Sets the original attribute ID of the ordered service attribute.
	 *
	 * @param string|null $id Attribute ID of the ordered service attribute
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setAttributeId( ?string $id ) : \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface;

	/**
	 * Returns the code of the service attribute item.
	 *
	 * @return string code of the service attribute item
	 */
	public function getCode() : string;

	/**
	 * Sets a new code for the service attribute item.
	 *
	 * @param string $code Code as defined by the service provider
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface;

	/**
	 * Returns the name of the service attribute item.
	 *
	 * @return string Name of the service attribute item
	 */
	public function getName() : string;

	/**
	 * Sets a new name for the service attribute item.
	 *
	 * @param string|null $name Name as defined by the service provider
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setName( ?string $name ) : \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface;

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
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setValue( $value ) : \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface;

	/**
	 * Returns the quantity of the service attribute.
	 *
	 * @return float Quantity of the service attribute
	 */
	public function getQuantity() : float;

	/**
	 * Sets the quantity of the service attribute.
	 *
	 * @param float $value Quantity of the service attribute
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setQuantity( float $value ) : \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface;

	/**
	 * Copys all data from a given attribute item.
	 *
	 * @param \Aimeos\MShop\Attribute\Item\Iface $item Attribute item to copy from
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Attribute\Item\Iface $item ) : \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface;
}
