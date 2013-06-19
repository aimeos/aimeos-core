<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Interface for objects storing the selected product attributes.
 *
 * @package MShop
 * @subpackage Order
 */
interface MShop_Order_Item_Base_Product_Attribute_Interface extends MShop_Common_Item_Interface
{
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
	 */
	public function setCode( $code );

	/**
	 * Returns the value of the product attribute.
	 *
	 * @return string Value of the product attribute
	 */
	public function getValue();

	/**
	 * Sets the value of the product attribute.
	 *
	 * @param string $value Value of the product attribute
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
	 */
	public function setName($name);

	/**
	 * Copys all data from a given attribute item.
	 *
	 * @param MShop_Attribute_Item_Interface $item Attribute item to copy from
	 */
	public function copyFrom( MShop_Attribute_Item_Interface $item );
}
