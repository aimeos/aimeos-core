<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base\Product\Attribute;


/**
 * Interface for objects storing the selected product attributes.
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
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface Order base product attribute item for chaining method calls
	 */
	public function setSiteId( string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface;

	/**
	 * Returns the original attribute ID of the ordered product attribute.
	 *
	 * @return string Attribute ID of the ordered product attribute
	 */
	public function getAttributeId() : string;

	/**
	 * Sets the original attribute ID of the ordered product attribute.
	 *
	 * @param string|null $id Attribute ID of the ordered product attribute
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface Order base product attribute item for chaining method calls
	 */
	public function setAttributeId( ?string $id ) : \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface;

	/**
	 * Returns the code of the product attibute.
	 *
	 * @return string Code of the attribute
	 */
	public function getCode() : string;

	/**
	 * Sets the code of the product attribute.
	 *
	 * @param string $code Code of the attribute
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface Order base product attribute item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface;

	/**
	 * Returns the localized name of the product attribute.
	 *
	 * @return string Localized name of the product attribute
	 */
	public function getName() : string;

	/**
	 * Sets the localized name of the product attribute.
	 *
	 * @param string|null $name Localized name of the product attribute
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface Order base product attribute item for chaining method calls
	 */
	public function setName( ?string $name ) : \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface;

	/**
	 * Returns the value of the product attribute.
	 *
	 * @return string|array Value of the product attribute
	 */
	public function getValue();

	/**
	 * Sets the value of the product attribute.
	 *
	 * @param string|array $value Value of the product attribute
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface Order base product attribute item for chaining method calls
	 */
	public function setValue( $value ) : \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface;

	/**
	 * Returns the quantity of the product attribute.
	 *
	 * @return float Quantity of the product attribute
	 */
	public function getQuantity() : float;

	/**
	 * Sets the quantity of the product attribute.
	 *
	 * @param float $value Quantity of the product attribute
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface Order base product attribute item for chaining method calls
	 */
	public function setQuantity( float $value ) : \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface;

	/**
	 * Copys all data from a given attribute item.
	 *
	 * @param \Aimeos\MShop\Attribute\Item\Iface $item Attribute item to copy from
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface Order base product attribute item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Attribute\Item\Iface $item ) : \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface;
}
