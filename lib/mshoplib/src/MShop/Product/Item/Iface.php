<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Product
 */


namespace Aimeos\MShop\Product\Item;


/**
 * Generic interface for product items created and saved by product managers.
 *
 * @package MShop
 * @subpackage Product
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Config\Iface,
		\Aimeos\MShop\Common\Item\ListsRef\Iface, \Aimeos\MShop\Common\Item\PropertyRef\Iface,
		\Aimeos\MShop\Common\Item\Rating\Iface, \Aimeos\MShop\Common\Item\Status\Iface,
		\Aimeos\MShop\Common\Item\Time\Iface, \Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Returns the catalog items referencing the product
	 *
	 * @return \Aimeos\Map Associative list of items implementing \Aimeos\MShop\Catalog\Item\Iface
	 */
	public function getCatalogItems() : \Aimeos\Map;

	/**
	 * Returns the supplier items referencing the product
	 *
	 * @return \Aimeos\Map Associative list of items implementing \Aimeos\MShop\Supplier\Item\Iface
	 */
	public function getSupplierItems() : \Aimeos\Map;

	/**
	 * Returns the stock items associated to the product
	 *
	 * @param string|null $type Type of the stock item
	 * @return \Aimeos\Map Associative list of items implementing \Aimeos\MShop\Stock\Item\Iface
	 */
	public function getStockItems( $type = null ) : \Aimeos\Map;

	/**
	 * Returns the code of the product item.
	 *
	 * @return string Code of the product
	 */
	public function getCode() : string;

	/**
	 * Sets a new code of the product item.
	 *
	 * @param string $code New code of the product item
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Product\Item\Iface;

	/**
	 * Returns the data set name assigned to the product item.
	 *
	 * @return string Data set name
	 */
	public function getDataset() : string;

	/**
	 * Sets a new data set name assignd to the product item.
	 *
	 * @param string $name New data set name
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setDataset( ?string $name ) : \Aimeos\MShop\Product\Item\Iface;

	/**
	 * Returns the label of the product item.
	 *
	 * @return string Label of the product item
	 */
	public function getLabel() : string;

	/**
	 * Sets a new label of the product.
	 *
	 * @param string $label New label of the product item
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Product\Item\Iface;

	/**
	 * Returns the URL segment for the product item.
	 *
	 * @return string URL segment of the product item
	 */
	public function getUrl() : string;

	/**
	 * Sets a new URL segment for the product.
	 *
	 * @param string|null $url New URL segment of the product item
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setUrl( ?string $url ) : \Aimeos\MShop\Product\Item\Iface;

	/**
	 * Returns the quantity scale of the product item.
	 *
	 * @return float Quantity scale
	 */
	public function getScale() : float;

	/**
	 * Sets a new quantity scale of the product item.
	 *
	 * @param float $value New quantity scale
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setScale( float $value ) : \Aimeos\MShop\Product\Item\Iface;

	/**
	 * Returns the URL target specific for that product
	 *
	 * @return string URL target specific for that product
	 */
	public function getTarget() : string;

	/**
	 * Sets a new label of the product item.
	 *
	 * @param string $value New URL target specific for that product
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setTarget( ?string $value ) : \Aimeos\MShop\Product\Item\Iface;

	/**
	 * Returns or sets a flag if stock is available for that product.
	 *
	 * @param int|null $value 0/1 to set value, null to return value
	 * @return int "0" if product is out of stock, "1" if product is in stock
	 */
	public function inStock( int $value = null ) : int;
}
