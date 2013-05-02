<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Basket item interface storing the product and the selected attributes and the computed price.
 *
 * @package MShop
 * @subpackage Order
 */
interface MShop_Order_Item_Base_Product_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the base ID.
	 *
	 * @return integer Base ID
	 */
	public function getBaseId();

	/**
	 * Sets the base ID.
	 *
	 * @param integer $baseid New base ID
	 */
	public function setBaseId( $baseid );

	/**
	 * Returns the parent ID of the ordered product if there is one.
	 * This ID relates to another product of the same order and provides a relation for e.g. sub-products in bundles.
	 *
	 * @return integer order product ID
	 */
	public function getOrderProductId();

	/**
	 * Sets the parent ID of the ordered product.
	 * This ID relates to another product of the same order and provides a relation for e.g. sub-products in bundles.
	 *
	 * @param integer|null Order product ID
	 */
	public function setOrderProductId( $orderProductId );

	/**
	 * Returns the type of the ordered product.
	 *
	 * @return string order product type
	 */
	public function getType();

	/**
	 * Sets the type of the ordered product.
	 *
	 * @param string $type order product type
	 */
	public function setType( $type );

	/**
	 * Returns the supplier code.
	 *
	 * @return string supplier code
	 */
	public function getSupplierCode();

	/**
	 * Sets the supplier code.
	 *
	 * @param string $suppliercode
	 */
	public function setSupplierCode( $suppliercode );

	/**
	 * Returns the product ID the customer has selected.
	 *
	 * @return string Original product ID
	 */
	public function getProductId();

	/**
	 * Sets the ID of a product the customer has selected.
	 *
	 * @param string $id Original product ID
	 */
	public function setProductId( $id );

	/**
	 * Returns the product code the customer has selected.
	 *
	 * @return string product code
	 */
	public function getProductCode();

	/**
	 * Sets the code of a product the customer has selected.
	 *
	 * @param string $code product code
	 */
	public function setProductCode( $code );

	/**
	 * Returns the localized name of the product
	 *
	 * @return string Returns the localized name of the product
	 */
	public function getName();

	/**
	 * Sets the localized name of the product.
	 *
	 * @param string $value Value of the localized name of the product
	 */
	public function setName($value);

	/**
	 * Sets the media url of the product the customer has added.
	 *
	 * @param string $value Location of the media/picture
	 */
	public function setMediaUrl( $value );

	/**
	 * Returns the location of the media.
	 *
	 * @return string Location of the media
	 */
	public function getMediaUrl();

	/**
	 * Returns the number of packages the customer has added.
	 *
	 * @return integer Amount of product packages
	 */
	public function getQuantity();

	/**
	 * Sets the number of product packages the customer has added.
	 *
	 * @param integer $quantitiy Amount of product packages
	 */
	public function setQuantity( $quantitiy );

	/**
	 * Returns the stored price item for the selected product and package.
	 *
	 * @return MShop_Price_Item_Interface Price item with price, shipping and rebate
	 */
	public function getPrice();

	/**
	 * Sets the new price item for the selected product and package.
	 *
	 * @param MShop_Price_Item_Interface $price Price item containing price and shipping costs
	 */
	public function setPrice( MShop_Price_Item_Interface $price );

	/**
	 * Returns the price item for the product whose values are multiplied with the quantity.
	 *
	 * @return MShop_Price_Item_Interface Price item with price, shipping and rebate
	 */
	public function getSumPrice();

	/**
	 *	Returns the set flags for the product item.
	 *
	 * @return integer Flags, e.g. for immutable products
	 */
	public function getFlags();

	/**
	 *	Sets the new value for the product item flags.
	 *
	 * @param integer $value Flags, e.g. for immutable products
	 */
	public function setFlags( $value );

	/**
	 * Returns the position of the product in the order.
	 *
	 * @return integer|null Product position in the order from 1-n
	 */
	public function getPosition();

	/**
	 * Returns the current delivery status of the order product item.
	 * The returned status values are the STAT_* constants from the
	 * MShop_Order_Item_Abstract class
	 *
	 * @return integer Delivery status of the product
	 */
	public function getStatus();

	/**
	 * Sets the new delivery status of the order product item.
	 * Possible status values are the STAT_* constants from the
	 * MShop_Order_Item_Abstract class
	 *
	 * @param integer $value New delivery status of the product
	 */
	public function setStatus( $value );

	/**
	 * Returns the value of the attribute item for the ordered product with the given code.
	 *
	 * @param string $code code of the product attribute item.
	 * @return string|null Value of the attribute item for the ordered product and the given code
	 */
	public function getAttribute( $code );

	/**
	 * Returns the attribute item for the ordered product with the given code.
	 *
	 * @param string $code code of the product attribute item.
	 * @return MShop_Order_Item_Base_Product_Attribute_Interface|null Attribute item for the ordered product and the given code
	 */
	public function getAttributeItem( $code );

	/**
	 * Returns the list of attribute items for the ordered product.
	 *
	 * @return array List of attribute items implementing MShop_Order_Item_Base_Product_Attribute_Interface
	 */
	public function getAttributes();

	/**
	 * Sets the new list of attribute items for the product.
	 *
	 * @param array $attributes List of attribute items implementing MShop_Order_Item_Base_Product_Attribute_Interface
	 */
	public function setAttributes( array $attributes );

	/**
	 * Copys all data from a given product.
	 *
	 * @param MShop_Product_Item_Interface $product New product
	 */
	public function copyFrom( MShop_Product_Item_Interface $product );
}
