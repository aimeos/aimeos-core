<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base\Product;


/**
 * Product item of order base
 * @package MShop
 * @subpackage Order
 */
class Standard extends Base implements Iface
{
	private $values;


	/**
	 * Initializes the order product instance.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item
	 * @param array $values Associative list of order product values
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface[] $attributes List of order product attribute items
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $products List of ordered subproduct items
	 */
	public function __construct( \Aimeos\MShop\Price\Item\Iface $price, array $values = [], array $attributes = [], array $products = [] )
	{
		parent::__construct( $price, $values, $attributes, $products );

		$this->values = $values;
	}


	/**
	 * Returns the ID of the site the item is stored
	 *
	 * @return string|null Site ID (or null if not available)
	 */
	public function getSiteId()
	{
		if( isset( $this->values['order.base.product.siteid'] ) ) {
			return (string) $this->values['order.base.product.siteid'];
		}
	}


	/**
	 * Sets the site ID of the item.
	 *
	 * @param string $value Unique site ID of the item
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setSiteId( $value )
	{
		if( (string) $value !== $this->getSiteId() )
		{
			$this->values['order.base.product.siteid'] = (string) $value;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the base ID.
	 *
	 * @return string|null Base ID
	 */
	public function getBaseId()
	{
		if( isset( $this->values['order.base.product.baseid'] ) ) {
			return (string) $this->values['order.base.product.baseid'];
		}
	}


	/**
	 * Sets the base order ID the product belongs to.
	 *
	 * @param string $value New order base ID
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setBaseId( $value )
	{
		if( (string) $value !== $this->getBaseId() )
		{
			$this->values['order.base.product.baseid'] = (string) $value;
			$this->setModified();
		}

		return $this;
	}

	/**
	 * Returns the order address ID the product should be shipped to
	 *
	 * @return string|null Order address ID
	 */
	public function getOrderAddressId()
	{
		if( isset( $this->values['order.base.product.orderaddressid'] ) ) {
			return (string) $this->values['order.base.product.orderaddressid'];
		}
	}


	/**
	 * Sets the order address ID the product should be shipped to
	 *
	 * @param string|null $value Order address ID
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setOrderAddressId( $value )
	{
		if( $value !== $this->getOrderAddressId() )
		{
			$this->values['order.base.product.orderaddressid'] = ( $value !== null ? (string) $value : null );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the parent ID of the ordered product if there is one.
	 * This ID relates to another product of the same order and provides a relation for e.g. sub-products in bundles.
	 *
	 * @return string|null Order product ID
	 */
	public function getOrderProductId()
	{
		if( isset( $this->values['order.base.product.orderproductid'] ) ) {
			return (string) $this->values['order.base.product.orderproductid'];
		}
	}


	/**
	 * Sets the parent ID of the ordered product.
	 * This ID relates to another product of the same order and provides a relation for e.g. sub-products in bundles.
	 *
	 * @param string|null $value Order product ID
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setOrderProductId( $value )
	{
		if( $value !== $this->getOrderProductId() )
		{
			$this->values['order.base.product.orderproductid'] = ( $value !== null ? (string) $value : null );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the type of the ordered product.
	 *
	 * @return string Type of the ordered product
	 */
	public function getType()
	{
		if( isset( $this->values['order.base.product.type'] ) ) {
			return (string) $this->values['order.base.product.type'];
		}

		return '';
	}


	/**
	 * Sets the type of the ordered product.
	 *
	 * @param string $type Type of the order product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setType( $type )
	{
		if( (string) $type !== $this->getType() )
		{
			$this->values['order.base.product.type'] = (string) $type;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the supplier code.
	 *
	 * @return string the code of supplier
	 */
	public function getSupplierCode()
	{
		if( isset( $this->values['order.base.product.suppliercode'] ) ) {
			return (string) $this->values['order.base.product.suppliercode'];
		}

		return '';
	}


	/**
	 * Sets the supplier code.
	 *
	 * @param string $suppliercode Code of supplier
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setSupplierCode( $suppliercode )
	{
		if( (string) $suppliercode !== $this->getSupplierCode() )
		{
			$this->values['order.base.product.suppliercode'] = (string) $this->checkCode( $suppliercode );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the product ID the customer has selected.
	 *
	 * @return string Original product ID
	 */
	public function getProductId()
	{
		if( isset( $this->values['order.base.product.productid'] ) ) {
			return (string) $this->values['order.base.product.productid'];
		}

		return '';
	}


	/**
	 * Sets the ID of a product the customer has selected.
	 *
	 * @param string $id Product Code ID
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setProductId( $id )
	{
		if( (string) $id !== $this->getProductId() )
		{
			$this->values['order.base.product.productid'] = (string) $id;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the product code the customer has selected.
	 *
	 * @return string Product code
	 */
	public function getProductCode()
	{
		if( isset( $this->values['order.base.product.prodcode'] ) ) {
			return (string) $this->values['order.base.product.prodcode'];
		}

		return '';
	}


	/**
	 * Sets the code of a product the customer has selected.
	 *
	 * @param string $code Product code
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setProductCode( $code )
	{
		if( (string) $code !== $this->getProductCode() )
		{
			$this->values['order.base.product.prodcode'] = (string) $this->checkCode( $code );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the code of the stock type the product should be retrieved from.
	 *
	 * @return string Stock type
	 */
	public function getStockType()
	{
		if( isset( $this->values['order.base.product.stocktype'] ) ) {
			return (string) $this->values['order.base.product.stocktype'];
		}

		return '';
	}


	/**
	 * Sets the code of the stock type the product should be retrieved from.
	 *
	 * @param string $code Stock type
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setStockType( $code )
	{
		if( (string) $code !== $this->getStockType() )
		{
			$this->values['order.base.product.stocktype'] = (string) $this->checkCode( $code );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the localized name of the product.
	 *
	 * @return string Returns the localized name of the product
	 */
	public function getName()
	{
		if( isset( $this->values['order.base.product.name'] ) ) {
			return (string) $this->values['order.base.product.name'];
		}

		return '';
	}


	/**
	 * Sets the localized name of the product.
	 *
	 * @param string $value Localized name of the product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setName( $value )
	{
		if( (string) $value !== $this->getName() )
		{
			$this->values['order.base.product.name'] = (string) $value;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the location of the media.
	 *
	 * @return string Location of the media
	 */
	public function getMediaUrl()
	{
		if( isset( $this->values['order.base.product.mediaurl'] ) ) {
			return (string) $this->values['order.base.product.mediaurl'];
		}

		return '';
	}


	/**
	 * Sets the media url of the product the customer has added.
	 *
	 * @param string $value Location of the media/picture
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setMediaUrl( $value )
	{
		if( (string) $value !== $this->getMediaUrl() )
		{
			$this->values['order.base.product.mediaurl'] = (string) $value;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the URL target specific for that product
	 *
	 * @return string URL target specific for that product
	 */
	public function getTarget()
	{
		if( isset( $this->values['order.base.product.target'] ) ) {
			return (string) $this->values['order.base.product.target'];
		}

		return '';
	}


	/**
	 * Sets the URL target specific for that product
	 *
	 * @param string $value New URL target specific for that product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setTarget( $value )
	{
		if( (string) $value !== $this->getTarget() )
		{
			$this->values['order.base.product.target'] = (string) $value;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the amount of products the customer has added.
	 *
	 * @return integer Amount of products
	 */
	public function getQuantity()
	{
		if( isset( $this->values['order.base.product.quantity'] ) ) {
			return (int) $this->values['order.base.product.quantity'];
		}

		return 1;
	}


	/**
	 * Sets the amount of products the customer has added.
	 *
	 * @param integer $quantity Amount of products
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setQuantity( $quantity )
	{
		if( $quantity < 1 || $quantity > 2147483647 ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Quantity must be a positive integer and must not exceed %1$d', 2147483647 ) );
		}

		if( (int) $quantity !== $this->getQuantity() )
		{
			$this->values['order.base.product.quantity'] = (int) $quantity;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * 	Returns the flags for the product item.
	 *
	 * @return integer Flags, e.g. for immutable products
	 */
	public function getFlags()
	{
		if( isset( $this->values['order.base.product.flags'] ) ) {
			return (int) $this->values['order.base.product.flags'];
		}

		return \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_NONE;
	}


	/**
	 * Sets the new value for the product item flags.
	 *
	 * @param integer $value Flags, e.g. for immutable products
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setFlags( $value )
	{
		if( (int) $value !== $this->getFlags() )
		{
			$this->values['order.base.product.flags'] = (int) $this->checkFlags( $value );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the position of the product in the order.
	 *
	 * @return integer|null Product position in the order from 1-n
	 */
	public function getPosition()
	{
		if( isset( $this->values['order.base.product.position'] ) ) {
			return (int) $this->values['order.base.product.position'];
		}
	}


	/**
	 * Sets the position of the product within the list of ordered products.
	 *
	 * @param integer|null $value Product position in the order from 1-n or null for resetting the position
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 * @throws \Aimeos\MShop\Order\Exception If there's already a position set
	 */
	public function setPosition( $value )
	{
		if( $value !== null && $value < 1 ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Order product position "%1$s" must be greater than 0', $value ) );
		}

		if( $value !== $this->getPosition() )
		{
			$this->values['order.base.product.position'] = ( $value !== null ? (int) $value : null );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the current delivery status of the order product item.
	 *
	 * The returned status values are the STAT_* constants from the
	 * \Aimeos\MShop\Order\Item\Base class
	 *
	 * @return integer Delivery status of the product
	 */
	public function getStatus()
	{
		if( isset( $this->values['order.base.product.status'] ) ) {
			return (int) $this->values['order.base.product.status'];
		}

		return \Aimeos\MShop\Order\Item\Base::STAT_UNFINISHED;
	}


	/**
	 * Sets the new delivery status of the order product item.
	 *
	 * Possible status values are the STAT_* constants from the
	 * \Aimeos\MShop\Order\Item\Base class
	 *
	 * @param integer $value New delivery status of the product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setStatus( $value )
	{
		if( (int) $value !== $this->getStatus() )
		{
			$this->values['order.base.product.status'] = (int) $value;
			$this->setModified();
		}

		return $this;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order product item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.base.product.siteid': !$private ?: $item = $item->setSiteId( $value ); break;
				case 'order.base.product.baseid': !$private ?: $item = $item->setBaseId( $value ); break;
				case 'order.base.product.orderproductid': !$private ?: $item = $item->setOrderProductId( $value ); break;
				case 'order.base.product.orderaddressid': !$private ?: $item = $item->setOrderAddressId( $value ); break;
				case 'order.base.product.flags': !$private ?: $item = $item->setFlags( $value ); break;
				case 'order.base.product.type': $item = $item->setType( $value ); break;
				case 'order.base.product.stocktype': $item = $item->setStockType( $value ); break;
				case 'order.base.product.suppliercode': $item = $item->setSupplierCode( $value ); break;
				case 'order.base.product.productid': $item = $item->setProductId( $value ); break;
				case 'order.base.product.prodcode': $item = $item->setProductCode( $value ); break;
				case 'order.base.product.name': $item = $item->setName( $value ); break;
				case 'order.base.product.mediaurl': $item = $item->setMediaUrl( $value ); break;
				case 'order.base.product.target': !$private ?: $item = $item->setTarget( $value ); break;
				case 'order.base.product.position': !$private ?: $item = $item->setPosition( $value ); break;
				case 'order.base.product.quantity': $item = $item->setQuantity( $value ); break;
				case 'order.base.product.status': $item = $item->setStatus( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as associative list.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
	{
		$list = parent::toArray( $private );

		$list['order.base.product.type'] = $this->getType();
		$list['order.base.product.stocktype'] = $this->getStockType();
		$list['order.base.product.suppliercode'] = $this->getSupplierCode();
		$list['order.base.product.prodcode'] = $this->getProductCode();
		$list['order.base.product.productid'] = $this->getProductId();
		$list['order.base.product.quantity'] = $this->getQuantity();
		$list['order.base.product.name'] = $this->getName();
		$list['order.base.product.mediaurl'] = $this->getMediaUrl();
		$list['order.base.product.status'] = $this->getStatus();
		$list['order.base.product.position'] = $this->getPosition();

		if( $private === true )
		{
			$list['order.base.product.baseid'] = $this->getBaseId();
			$list['order.base.product.orderproductid'] = $this->getOrderProductId();
			$list['order.base.product.orderaddressid'] = $this->getOrderAddressId();
			$list['order.base.product.target'] = $this->getTarget();
			$list['order.base.product.flags'] = $this->getFlags();
		}

		return $list;
	}

	/**
	 * Compares the properties of the given order product item with its own ones.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $item Order product item
	 * @return boolean True if the item properties are equal, false if not
	 * @since 2014.09
	 */
	public function compare( \Aimeos\MShop\Order\Item\Base\Product\Iface $item )
	{
		if( $this->getFlags() === $item->getFlags()
			&& $this->getName() === $item->getName()
			&& $this->getSiteId() === $item->getSiteId()
			&& $this->getStockType() === $item->getStockType()
			&& $this->getProductCode() === $item->getProductCode()
			&& $this->getSupplierCode() === $item->getSupplierCode()
		) {
			return true;
		}

		return false;
	}


	/**
	 * Copys all data from a given product item.
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $product Product item to copy from
	 */
	public function copyFrom( \Aimeos\MShop\Product\Item\Iface $product )
	{
		$this->setSiteId( $product->getSiteId() );
		$this->setProductCode( $product->getCode() );
		$this->setProductId( $product->getId() );
		$this->setType( $product->getType() );
		$this->setTarget( $product->getTarget() );
		$this->setName( $product->getName() );

		$items = $product->getRefItems( 'text', 'basket', 'default' );
		if( ( $item = reset( $items ) ) !== false ) {
			$this->setName( $item->getContent() );
		}

		$items = $product->getRefItems( 'media', 'default', 'default' );
		if( ( $item = reset( $items ) ) !== false ) {
			$this->setMediaUrl( $item->getPreview() );
		}

		return $this->setModified();
	}
}
