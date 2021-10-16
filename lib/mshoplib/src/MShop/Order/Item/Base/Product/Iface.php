<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base\Product;


/**
 * Basket item interface storing the product and the selected attributes and the computed price.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Returns the associated product item
	 *
	 * @return \Aimeos\MShop\Product\Item\Iface|null Product item
	 */
	public function getProductItem() : ?\Aimeos\MShop\Product\Item\Iface;

	/**
	 * Returns the associated supplier item
	 *
	 * @return \Aimeos\MShop\Supplier\Item\Iface|null Supplier item
	 */
	public function getSupplierItem() : ?\Aimeos\MShop\Supplier\Item\Iface;

	/**
	 * Sets the site ID of the item.
	 *
	 * @param string $value Unique site ID of the item
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setSiteId( string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the base ID.
	 *
	 * @return string|null Base ID
	 */
	public function getBaseId() : ?string;

	/**
	 * Sets the base ID.
	 *
	 * @param string $baseid|null New base ID
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setBaseId( ?string $baseid ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the order address ID the product should be shipped to
	 *
	 * @return string|null Order address ID
	 */
	public function getOrderAddressId() : ?string;

	/**
	 * Sets the order address ID the product should be shipped to
	 *
	 * @param string|null $value Order address ID
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setOrderAddressId( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the parent ID of the ordered product if there is one.
	 * This ID relates to another product of the same order and provides a relation for e.g. sub-products in bundles.
	 *
	 * @return string|null order product ID
	 */
	public function getOrderProductId() : ?string;

	/**
	 * Sets the parent ID of the ordered product.
	 * This ID relates to another product of the same order and provides a relation for e.g. sub-products in bundles.
	 *
	 * @param string|null $value Order product ID
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setOrderProductId( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the supplier ID.
	 *
	 * @return string Supplier ID
	 */
	public function getSupplierId() : string;

	/**
	 * Sets the supplier ID.
	 *
	 * @param string|null $value Supplier ID
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setSupplierId( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the supplier name.
	 *
	 * @return string Supplier name
	 */
	public function getSupplierName() : string;

	/**
	 * Sets the supplier name.
	 *
	 * @param string|null $value Supplier name
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setSupplierName( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the position of the product in the order.
	 *
	 * @return int|null Product position in the order from 0-n
	 */
	public function getPosition() : ?int;

	/**
	 * Sets the position of the product within the list of ordered products.
	 *
	 * @param int|null $value Product position in the order from 0-n or null for resetting the position
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setPosition( ?int $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the product ID the customer has selected.
	 *
	 * @return string Original product ID
	 */
	public function getProductId() : string;

	/**
	 * Sets the ID of a product the customer has selected.
	 *
	 * @param string|null $id Original product ID
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setProductId( ?string $id ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the product ID of the parent product.
	 *
	 * @return string Product ID of the parent product
	 */
	public function getParentProductId() : string;

	/**
	 * Sets the ID of the parent product the customer has selected.
	 *
	 * @param string|null $id Product ID of the parent product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setParentProductId( ?string $id ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the product code the customer has selected.
	 *
	 * @return string product code
	 */
	public function getProductCode() : string;

	/**
	 * Sets the code of a product the customer has selected.
	 *
	 * @param string $code product code
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setProductCode( string $code ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns all of sub-product items
	 *
	 * @return \Aimeos\Map List of product items implementing \Aimeos\MShop\Order\Item\Base\Product\Iface
	 */
	public function getProducts() : \Aimeos\Map;

	/**
	 * Sets all sub-product items
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $products List of product items
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setProducts( iterable $products ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the code of the stock type the product should be retrieved from.
	 *
	 * @return string Stock type
	 */
	public function getStockType() : string;

	/**
	 * Returns the expected delivery time frame
	 *
	 * @return string Expected delivery time frame
	 */
	public function getTimeframe() : string;

	/**
	 * Sets the expected delivery time frame
	 *
	 * @param string|null $timeframe Expected delivery time frame
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setTimeframe( ?string $timeframe ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Sets the code of the stock type the product should be retrieved from.
	 *
	 * @param string|null $code Stock type
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setStockType( ?string $code ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the localized name of the product
	 *
	 * @return string Returns the localized name of the product
	 */
	public function getName() : string;

	/**
	 * Sets the localized name of the product.
	 *
	 * @param string|null $value Value of the localized name of the product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setName( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the localized description of the product
	 *
	 * @return string Returns the localized description of the product
	 */
	public function getDescription() : string;

	/**
	 * Sets the localized description of the product.
	 *
	 * @param string|null $value Value of the localized description of the product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setDescription( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the location of the media.
	 *
	 * @return string Location of the media
	 */
	public function getMediaUrl() : string;

	/**
	 * Sets the media url of the product the customer has added.
	 *
	 * @param string|null $value Location of the media/picture
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setMediaUrl( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the URL target specific for that product
	 *
	 * @return string URL target specific for that product
	 */
	public function getTarget() : string;

	/**
	 * Sets the URL target specific for that product
	 *
	 * @param string|null $value New URL target specific for that product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setTarget( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the number of packages the customer has added.
	 *
	 * @return float Amount of product packages
	 */
	public function getQuantity() : float;

	/**
	 * Sets the number of product packages the customer has added.
	 *
	 * @param float $quantitiy Amount of product packages
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setQuantity( float $quantitiy ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the number of packages not yet delivered to the customer.
	 *
	 * @return float Amount of product packages
	 */
	public function getQuantityOpen() : float;

	/**
	 * Sets the number of product packages not yet delivered to the customer.
	 *
	 * @param float $quantitiy Amount of product packages
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setQuantityOpen( float $quantitiy ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the stored price item for the selected product and package.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface Price item with price, additional costs and rebate
	 */
	public function getPrice() : \Aimeos\MShop\Price\Item\Iface;

	/**
	 * Sets the new price item for the selected product and package.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item containing price and additional costs
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setPrice( \Aimeos\MShop\Price\Item\Iface $price ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 *	Returns the set flags for the product item.
	 *
	 * @return int Flags, e.g. for immutable products
	 */
	public function getFlags() : int;

	/**
	 *	Sets the new value for the product item flags.
	 *
	 * @param int $value Flags, e.g. for immutable products
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setFlags( int $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the notes for the ordered product.
	 *
	 * @return string Notes for the ordered product
	 */
	public function getNotes() : string;

	/**
	 * Sets the notes for the ordered product.
	 *
	 * @param string|null $value Notes for the ordered product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setNotes( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the current delivery status of the order product item.
	 *
	 * The returned status values are the STAT_* constants from the \Aimeos\MShop\Order\Item\Base class
	 *
	 * @return int|null Delivery status of the product
	 */
	public function getStatusDelivery() : ?int;

	/**
	 * Sets the new delivery status of the order product item.
	 *
	 * Possible status values are the STAT_* constants from the \Aimeos\MShop\Order\Item\Base class
	 *
	 * @param int|null $value New delivery status of the product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setStatusDelivery( ?int $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the current payment status of the order product item.
	 *
	 * The returned status values are the PAY_* constants from the \Aimeos\MShop\Order\Item\Base class
	 *
	 * @return int|null Payment status of the product
	 */
	public function getStatusPayment() : ?int;

	/**
	 * Sets the new payment status of the order product item.
	 *
	 * Possible status values are the PAY_* constants from the \Aimeos\MShop\Order\Item\Base class
	 *
	 * @param int|null $value New payment status of the product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setStatusPayment( ?int $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Returns the value of the attribute item for the ordered product with the given code.
	 *
	 * @param string $code Code of the product attribute item
	 * @param array|string $type Type or list of types of the product attribute items
	 * @return array|string|null Value of the attribute item for the ordered product and the given code
	 */
	public function getAttribute( string $code, $type = '' );

	/**
	 * Returns the attribute item for the ordered product with the given code.
	 *
	 * @param string $code Code of the product attribute item
	 * @param array|string $type Type or list of types of the product attribute items
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface|array|null Attribute item for the ordered product and the given code
	 */
	public function getAttributeItem( string $code, $type = '' );

	/**
	 * Returns the list of attribute items for the ordered product.
	 *
	 * @param string|null $type Filters returned attributes by the given type or null for no filtering
	 * @return \Aimeos\Map List of attribute items implementing \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface
	 */
	public function getAttributeItems( string $type = null ) : \Aimeos\Map;

	/**
	 * Adds or replaces the attribute item in the list of product attributes.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface $item Product attribute item
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setAttributeItem( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface $item ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Sets the new list of attribute items for the product.
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface[] $attributes List of order product attribute items
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setAttributeItems( iterable $attributes ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Copys all data from a given product.
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $product New product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Product\Item\Iface $product ) : \Aimeos\MShop\Order\Item\Base\Product\Iface;

	/**
	 * Compares the properties of the given order product item with its own ones.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $item Order product item
	 * @return bool True if the item properties are equal, false if not
	 * @since 2014.09
	 */
	public function compare( \Aimeos\MShop\Order\Item\Base\Product\Iface $item ) : bool;
}
