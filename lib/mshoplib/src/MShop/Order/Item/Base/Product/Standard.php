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
	}


	/**
	 * Returns the ID of the site the item is stored
	 *
	 * @return string|null Site ID (or null if not available)
	 */
	public function getSiteId()
	{
		return $this->get( 'order.base.product.siteid' );
	}


	/**
	 * Sets the site ID of the item.
	 *
	 * @param string $value Unique site ID of the item
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setSiteId( $value )
	{
		return $this->set( 'order.base.product.siteid', (string) $value );
	}


	/**
	 * Returns the base ID.
	 *
	 * @return string|null Base ID
	 */
	public function getBaseId()
	{
		return $this->get( 'order.base.product.baseid' );
	}


	/**
	 * Sets the base order ID the product belongs to.
	 *
	 * @param string $value New order base ID
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setBaseId( $value )
	{
		return $this->set( 'order.base.product.baseid', (string) $value );
	}


	/**
	 * Returns the order address ID the product should be shipped to
	 *
	 * @return string|null Order address ID
	 */
	public function getOrderAddressId()
	{
		return $this->get( 'order.base.product.orderaddressid' );
	}


	/**
	 * Sets the order address ID the product should be shipped to
	 *
	 * @param string|null $value Order address ID
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setOrderAddressId( $value )
	{
		return $this->set( 'order.base.product.orderaddressid', ( $value !== null ? (string) $value : null ) );
	}


	/**
	 * Returns the parent ID of the ordered product if there is one.
	 * This ID relates to another product of the same order and provides a relation for e.g. sub-products in bundles.
	 *
	 * @return string|null Order product ID
	 */
	public function getOrderProductId()
	{
		return $this->get( 'order.base.product.orderproductid' );
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
		return $this->set( 'order.base.product.orderproductid', ( $value !== null ? (string) $value : null ) );
	}


	/**
	 * Returns the type of the ordered product.
	 *
	 * @return string Type of the ordered product
	 */
	public function getType()
	{
		return (string) $this->get( 'order.base.product.type', '' );
	}


	/**
	 * Sets the type of the ordered product.
	 *
	 * @param string $type Type of the order product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setType( $type )
	{
		return $this->set( 'order.base.product.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns the supplier code.
	 *
	 * @return string the code of supplier
	 */
	public function getSupplierCode()
	{
		return (string) $this->get( 'order.base.product.suppliercode', '' );
	}


	/**
	 * Sets the supplier code.
	 *
	 * @param string $suppliercode Code of supplier
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setSupplierCode( $suppliercode )
	{
		return $this->set( 'order.base.product.suppliercode', $this->checkCode( $suppliercode ) );
	}


	/**
	 * Returns the product ID the customer has selected.
	 *
	 * @return string Original product ID
	 */
	public function getProductId()
	{
		return (string) $this->get( 'order.base.product.productid', '' );
	}


	/**
	 * Sets the ID of a product the customer has selected.
	 *
	 * @param string $id Product Code ID
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setProductId( $id )
	{
		return $this->set( 'order.base.product.productid', (string) $id );
	}


	/**
	 * Returns the product code the customer has selected.
	 *
	 * @return string Product code
	 */
	public function getProductCode()
	{
		return (string) $this->get( 'order.base.product.prodcode', '' );
	}


	/**
	 * Sets the code of a product the customer has selected.
	 *
	 * @param string $code Product code
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setProductCode( $code )
	{
		return $this->set( 'order.base.product.prodcode', $this->checkCode( $code ) );
	}


	/**
	 * Returns the code of the stock type the product should be retrieved from.
	 *
	 * @return string Stock type
	 */
	public function getStockType()
	{
		return (string) $this->get( 'order.base.product.stocktype', '' );
	}


	/**
	 * Sets the code of the stock type the product should be retrieved from.
	 *
	 * @param string $code Stock type
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setStockType( $code )
	{
		return $this->set( 'order.base.product.stocktype', $this->checkCode( $code ) );
	}


	/**
	 * Returns the localized name of the product.
	 *
	 * @return string Returns the localized name of the product
	 */
	public function getName()
	{
		return (string) $this->get( 'order.base.product.name', '' );
	}


	/**
	 * Sets the localized name of the product.
	 *
	 * @param string $value Localized name of the product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setName( $value )
	{
		return $this->set( 'order.base.product.name', (string) $value );
	}


	/**
	 * Returns the localized description of the product.
	 *
	 * @return string Returns the localized description of the product
	 */
	public function getDescription()
	{
		return (string) $this->get( 'order.base.product.description', '' );
	}


	/**
	 * Sets the localized description of the product.
	 *
	 * @param string $value Localized description of the product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setDescription( $value )
	{
		return $this->set( 'order.base.product.description', (string) $value );
	}


	/**
	 * Returns the location of the media.
	 *
	 * @return string Location of the media
	 */
	public function getMediaUrl()
	{
		return (string) $this->get( 'order.base.product.mediaurl', '' );
	}


	/**
	 * Sets the media url of the product the customer has added.
	 *
	 * @param string $value Location of the media/picture
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setMediaUrl( $value )
	{
		return $this->set( 'order.base.product.mediaurl', (string) $value );
	}


	/**
	 * Returns the URL target specific for that product
	 *
	 * @return string URL target specific for that product
	 */
	public function getTarget()
	{
		return (string) $this->get( 'order.base.product.target', '' );
	}


	/**
	 * Sets the URL target specific for that product
	 *
	 * @param string $value New URL target specific for that product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setTarget( $value )
	{
		return $this->set( 'order.base.product.target', (string) $value );
	}


	/**
	 * Returns the expected delivery time frame
	 *
	 * @return string Expected delivery time frame
	 */
	public function getTimeframe()
	{
		return (string) $this->get( 'order.base.product.timeframe', '' );
	}


	/**
	 * Sets the expected delivery time frame
	 *
	 * @param string $timeframe Expected delivery time frame
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setTimeframe( $timeframe )
	{
		return $this->set( 'order.base.product.timeframe', (string) $timeframe );
	}


	/**
	 * Returns the amount of products the customer has added.
	 *
	 * @return integer Amount of products
	 */
	public function getQuantity()
	{
		return (int) $this->get( 'order.base.product.quantity', 1 );
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

		return $this->set( 'order.base.product.quantity', (int) $quantity );
	}


	/**
	 * 	Returns the flags for the product item.
	 *
	 * @return integer Flags, e.g. for immutable products
	 */
	public function getFlags()
	{
		return (int) $this->get( 'order.base.product.flags', \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_NONE );
	}


	/**
	 * Sets the new value for the product item flags.
	 *
	 * @param integer $value Flags, e.g. for immutable products
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setFlags( $value )
	{
		return $this->set( 'order.base.product.flags', (int) $this->checkFlags( $value ) );
	}


	/**
	 * Returns the position of the product in the order.
	 *
	 * @return integer|null Product position in the order from 0-n
	 */
	public function getPosition()
	{
		return $this->get( 'order.base.product.position' );
	}


	/**
	 * Sets the position of the product within the list of ordered products.
	 *
	 * @param integer|null $value Product position in the order from 0-n or null for resetting the position
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 * @throws \Aimeos\MShop\Order\Exception If the position is invalid
	 */
	public function setPosition( $value )
	{
		if( $value < 0 ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Order product position "%1$s" must be greater than 0', $value ) );
		}

		return $this->set( 'order.base.product.position', ( $value !== null ? (int) $value : null ) );
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
		return (int) $this->get( 'order.base.product.status', \Aimeos\MShop\Order\Item\Base::STAT_UNFINISHED );
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
		return $this->set( 'order.base.product.status', (int) $value );
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
				case 'order.base.product.description': $item = $item->setDescription( $value ); break;
				case 'order.base.product.mediaurl': $item = $item->setMediaUrl( $value ); break;
				case 'order.base.product.timeframe': $item = $item->setTimeFrame( $value ); break;
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
		$list['order.base.product.description'] = $this->getDescription();
		$list['order.base.product.mediaurl'] = $this->getMediaUrl();
		$list['order.base.product.timeframe'] = $this->getTimeFrame();
		$list['order.base.product.position'] = $this->getPosition();
		$list['order.base.product.status'] = $this->getStatus();

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
			&& $this->getOrderAddressId() === $item->getOrderAddressId()
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
			$this->setDescription( $item->getContent() );
		}

		$items = $product->getRefItems( 'media', 'default', 'default' );
		if( ( $item = reset( $items ) ) !== false ) {
			$this->setMediaUrl( $item->getPreview() );
		}

		return $this->setModified();
	}
}
