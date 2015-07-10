<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Order
 */


/**
 * Interface for objects storing the selected product attributes.
 *
 * @package MShop
 * @subpackage Order
 */
interface MShop_Order_Item_Base_Product_Attribute_Interface
	extends MShop_Common_Item_Interface
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
	 * Returns the product ID of the ordered product.
	 *
	 * @return string Product ID of the ordered product
	 */
	public function getProductId();

	/**
	 * Sets the product ID of the ordered product.
	 *
	 * @param string $id product ID of the ordered product
	 * @return void
	 */
	public function setProductId( $id );

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
	public function setValue($value);

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
	public function setName($name);

	/**
	 * Copys all data from a given attribute item.
	 *
	 * @param MShop_Attribute_Item_Interface $item Attribute item to copy from
	 * @return void
	 */
	public function copyFrom( MShop_Attribute_Item_Interface $item );
}
