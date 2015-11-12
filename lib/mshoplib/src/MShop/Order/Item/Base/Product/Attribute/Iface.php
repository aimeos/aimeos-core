<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Parentid\Iface
{
	/**
	 * Returns the original attribute ID of the ordered product attribute.
	 *
	 * @return string Attribute ID of the ordered product attribute
	 */
	public function getAttributeId();

	/**
	 * Sets the original attribute ID of the ordered product attribute.
	 *
	 * @param string $id Attribute ID of the ordered product attribute
	 * @return void
	 */
	public function setAttributeId( $id );

	/**
	 * Returns the type of the product attibute.
	 *
	 * @return string Type of the attribute
	 */
	public function getType();

	/**
	 * Sets the type of the product attribute.
	 *
	 * @param string $type Type of the attribute
	 * @return void
	 */
	public function setType( $type );

	/**
	 * Returns the code of the product attibute.
	 *
	 * @return string Code of the attribute
	 */
	public function getCode();

	/**
	 * Sets the code of the product attribute.
	 *
	 * @param string $code Code of the attribute
	 * @return void
	 */
	public function setCode( $code );

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
	 * @return void
	 */
	public function setValue( $value );

	/**
	 * Returns the localized name of the product attribute.
	 *
	 * @return string Localized name of the product attribute
	 */
	public function getName();

	/**
	 * Sets the localized name of the product attribute.
	 *
	 * @param string $name Localized name of the product attribute
	 * @return void
	 */
	public function setName( $name );

	/**
	 * Copys all data from a given attribute item.
	 *
	 * @param \Aimeos\MShop\Attribute\Item\Iface $item Attribute item to copy from
	 * @return void
	 */
	public function copyFrom( \Aimeos\MShop\Attribute\Item\Iface $item );
}
