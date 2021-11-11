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
	 * Returns the associated product item
	 *
	 * @return \Aimeos\MShop\Product\Item\Iface|null Product item
	 */
	public function getProductItem() : ?\Aimeos\MShop\Product\Item\Iface
	{
		return $this->get( '.product' );
	}


	/**
	 * Returns the associated supplier item
	 *
	 * @return \Aimeos\MShop\Supplier\Item\Iface|null Supplier item
	 */
	public function getSupplierItem() : ?\Aimeos\MShop\Supplier\Item\Iface
	{
		return $this->get( '.supplier' );
	}


	/**
	 * Returns the ID of the site the item is stored
	 *
	 * @return string Site ID (or null if not available)
	 */
	public function getSiteId() : string
	{
		return $this->get( 'order.base.product.siteid', '' );
	}


	/**
	 * Sets the site ID of the item.
	 *
	 * @param string $value Unique site ID of the item
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setSiteId( string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.siteid', $value );
	}


	/**
	 * Returns the base ID.
	 *
	 * @return string|null Base ID
	 */
	public function getBaseId() : ?string
	{
		return $this->get( 'order.base.product.baseid' );
	}


	/**
	 * Sets the base order ID the product belongs to.
	 *
	 * @param string|null $value New order base ID
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setBaseId( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.baseid', $value );
	}


	/**
	 * Returns the order address ID the product should be shipped to
	 *
	 * @return string|null Order address ID
	 */
	public function getOrderAddressId() : ?string
	{
		return $this->get( 'order.base.product.orderaddressid' );
	}


	/**
	 * Sets the order address ID the product should be shipped to
	 *
	 * @param string|null $value Order address ID
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setOrderAddressId( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.orderaddressid', ( $value !== null ? $value : null ) );
	}


	/**
	 * Returns the parent ID of the ordered product if there is one.
	 * This ID relates to another product of the same order and provides a relation for e.g. sub-products in bundles.
	 *
	 * @return string|null Order product ID
	 */
	public function getOrderProductId() : ?string
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
	public function setOrderProductId( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.orderproductid', ( $value !== null ? $value : null ) );
	}


	/**
	 * Returns the type of the ordered product.
	 *
	 * @return string Type of the ordered product
	 */
	public function getType() : string
	{
		return $this->get( 'order.base.product.type', '' );
	}


	/**
	 * Sets the type of the ordered product.
	 *
	 * @param string $type Type of the order product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'order.base.product.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns the supplier ID.
	 *
	 * @return string The ID of the supplier
	 */
	public function getSupplierId() : string
	{
		return $this->get( 'order.base.product.supplierid', '' );
	}


	/**
	 * Sets the supplier ID.
	 *
	 * @param string|null $value ID of the supplier
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setSupplierId( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.supplierid', (string) $value );
	}


	/**
	 * Returns the supplier name.
	 *
	 * @return string Supplier name
	 */
	public function getSupplierName() : string
	{
		return $this->get( 'order.base.product.suppliername', '' );
	}


	/**
	 * Sets the supplier name.
	 *
	 * @param string|null $value Supplier name
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setSupplierName( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.suppliername', (string) $value );
	}


	/**
	 * Returns the product ID the customer has selected.
	 *
	 * @return string Original product ID
	 */
	public function getProductId() : string
	{
		return $this->get( 'order.base.product.productid', '' );
	}


	/**
	 * Sets the ID of a product the customer has selected.
	 *
	 * @param string|null $id Product Code ID
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setProductId( ?string $id ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.productid', (string) $id );
	}


	/**
	 * Returns the product ID of the parent product.
	 *
	 * @return string Product ID of the parent product
	 */
	public function getParentProductId() : string
	{
		return $this->get( 'order.base.product.parentproductid', '' );
	}


	/**
	 * Sets the ID of the parent product the customer has selected.
	 *
	 * @param string|null $id Product ID of the parent product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setParentProductId( ?string $id ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.parentproductid', (string) $id );
	}


	/**
	 * Returns the product code the customer has selected.
	 *
	 * @return string Product code
	 */
	public function getProductCode() : string
	{
		return $this->get( 'order.base.product.prodcode', '' );
	}


	/**
	 * Sets the code of a product the customer has selected.
	 *
	 * @param string $code Product code
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setProductCode( string $code ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.prodcode', $this->checkCode( $code ) );
	}


	/**
	 * Returns the code of the stock type the product should be retrieved from.
	 *
	 * @return string Stock type
	 */
	public function getStockType() : string
	{
		return $this->get( 'order.base.product.stocktype', '' );
	}


	/**
	 * Sets the code of the stock type the product should be retrieved from.
	 *
	 * @param string|null $code Stock type
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setStockType( ?string $code ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.stocktype', $this->checkCode( (string) $code ) );
	}


	/**
	 * Returns the localized name of the product.
	 *
	 * @return string Returns the localized name of the product
	 */
	public function getName() : string
	{
		return $this->get( 'order.base.product.name', '' );
	}


	/**
	 * Sets the localized name of the product.
	 *
	 * @param string|null $value Localized name of the product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setName( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.name', (string) $value );
	}


	/**
	 * Returns the localized description of the product.
	 *
	 * @return string Returns the localized description of the product
	 */
	public function getDescription() : string
	{
		return $this->get( 'order.base.product.description', '' );
	}


	/**
	 * Sets the localized description of the product.
	 *
	 * @param string|null $value Localized description of the product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setDescription( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.description', (string) $value );
	}


	/**
	 * Returns the location of the media.
	 *
	 * @return string Location of the media
	 */
	public function getMediaUrl() : string
	{
		return $this->get( 'order.base.product.mediaurl', '' );
	}


	/**
	 * Sets the media url of the product the customer has added.
	 *
	 * @param string|null $value Location of the media/picture
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setMediaUrl( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.mediaurl', (string) $value );
	}


	/**
	 * Returns the URL target specific for that product
	 *
	 * @return string URL target specific for that product
	 */
	public function getTarget() : string
	{
		return $this->get( 'order.base.product.target', '' );
	}


	/**
	 * Sets the URL target specific for that product
	 *
	 * @param string|null $value New URL target specific for that product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setTarget( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.target', (string) $value );
	}


	/**
	 * Returns the expected delivery time frame
	 *
	 * @return string Expected delivery time frame
	 */
	public function getTimeframe() : string
	{
		return $this->get( 'order.base.product.timeframe', '' );
	}


	/**
	 * Sets the expected delivery time frame
	 *
	 * @param string|null $timeframe Expected delivery time frame
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setTimeframe( ?string $timeframe ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.timeframe', (string) $timeframe );
	}


	/**
	 * Returns the amount of products the customer has added.
	 *
	 * @return float Amount of products
	 */
	public function getQuantity() : float
	{
		return (float) $this->get( 'order.base.product.quantity', 1 );
	}


	/**
	 * Sets the amount of products the customer has added.
	 *
	 * @param float $quantity Amount of products
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setQuantity( float $quantity ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		if( $quantity <= 0 || $quantity > 2147483647 ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Quantity must be greater than 0 and must not exceed 2147483647' ) );
		}

		return $this->set( 'order.base.product.quantity', $quantity );
	}


	/**
	 * Returns the number of packages not yet delivered to the customer.
	 *
	 * @return float Amount of product packages
	 */
	public function getQuantityOpen() : float
	{
		return (float) $this->get( 'order.base.product.qtyopen', $this->getQuantity() );
	}


	/**
	 * Sets the number of product packages not yet delivered to the customer.
	 *
	 * @param float $quantity Amount of product packages
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setQuantityOpen( float $quantity ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		if( $quantity < 0 || $quantity > $this->getQuantity() ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Quantity must be 0 or greater and must not exceed ordered quantity' ) );
		}

		return $this->set( 'order.base.product.qtyopen', $quantity );
	}


	/**
	 * 	Returns the flags for the product item.
	 *
	 * @return int Flags, e.g. for immutable products
	 */
	public function getFlags() : int
	{
		return $this->get( 'order.base.product.flags', \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_NONE );
	}


	/**
	 * Sets the new value for the product item flags.
	 *
	 * @param int $value Flags, e.g. for immutable products
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setFlags( int $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.flags', $this->checkFlags( $value ) );
	}


	/**
	 * Returns the position of the product in the order.
	 *
	 * @return int|null Product position in the order from 0-n
	 */
	public function getPosition() : ?int
	{
		return $this->get( 'order.base.product.position' );
	}


	/**
	 * Sets the position of the product within the list of ordered products.
	 *
	 * @param int|null $value Product position in the order from 0-n or null for resetting the position
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 * @throws \Aimeos\MShop\Order\Exception If the position is invalid
	 */
	public function setPosition( ?int $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		if( $value < 0 ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Order product position "%1$s" must be greater than 0', $value ) );
		}

		return $this->set( 'order.base.product.position', ( $value !== null ? $value : null ) );
	}


	/**
	 * Returns the current delivery status of the order product item.
	 *
	 * The returned status values are the STAT_* constants from the \Aimeos\MShop\Order\Item\Base class
	 *
	 * @return int|null Delivery status of the product
	 */
	public function getStatusDelivery() : ?int
	{
		return $this->get( 'order.base.product.statusdelivery' );
	}


	/**
	 * Sets the new delivery status of the order product item.
	 *
	 * Possible status values are the STAT_* constants from the \Aimeos\MShop\Order\Item\Base class
	 *
	 * @param int|null $value New delivery status of the product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setStatusDelivery( ?int $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.statusdelivery', $value );
	}


	/**
	 * @deprecated 2022.01
	 */
	public function getStatus() : int
	{
		$result = $this->get( 'order.base.product.statusdelivery' );
		return $result !== null ? $result : \Aimeos\MShop\Order\Item\Base::STAT_UNFINISHED;
	}


	/**
	 * @deprecated 2022.01
	 */
	public function setStatus( int $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->setStatusDelivery( $value );
	}


	/**
	 * Returns the current payment status of the order product item.
	 *
	 * The returned status values are the PAY_* constants from the \Aimeos\MShop\Order\Item\Base class
	 *
	 * @return int|null Payment status of the product
	 */
	public function getStatusPayment() : ?int
	{
		return $this->get( 'order.base.product.statuspayment' );
	}


	/**
	 * Sets the new payment status of the order product item.
	 *
	 * Possible status values are the PAY_* constants from the \Aimeos\MShop\Order\Item\Base class
	 *
	 * @param int|null $value New payment status of the product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setStatusPayment( ?int $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.statuspayment', $value );
	}


	/**
	 * Returns the notes for the ordered product.
	 *
	 * @return string Notes for the ordered product
	 */
	public function getNotes() : string
	{
		return $this->get( 'order.base.product.notes', '' );
	}


	/**
	 * Sets the notes for the ordered product.
	 *
	 * @param string|null $value Notes for the ordered product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setNotes( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return $this->set( 'order.base.product.notes', (string) $value );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order product item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
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
				case 'order.base.product.position': !$private ?: $item = $item->setPosition( (int) $value ); break;
				case 'order.base.product.flags': !$private ?: $item = $item->setFlags( (int) $value ); break;
				case 'order.base.product.target': !$private ?: $item = $item->setTarget( $value ); break;
				case 'order.base.product.parentproductid': $item = $item->setParentProductId( $value ); break;
				case 'order.base.product.productid': $item = $item->setProductId( $value ); break;
				case 'order.base.product.prodcode': $item = $item->setProductCode( $value ); break;
				case 'order.base.product.supplierid': $item = $item->setSupplierId( $value ); break;
				case 'order.base.product.suppliername': $item = $item->setSupplierName( $value ); break;
				case 'order.base.product.stocktype': $item = $item->setStockType( $value ); break;
				case 'order.base.product.type': $item = $item->setType( $value ); break;
				case 'order.base.product.name': $item = $item->setName( $value ); break;
				case 'order.base.product.description': $item = $item->setDescription( $value ); break;
				case 'order.base.product.mediaurl': $item = $item->setMediaUrl( $value ); break;
				case 'order.base.product.timeframe': $item = $item->setTimeFrame( $value ); break;
				case 'order.base.product.quantity': $item = $item->setQuantity( (float) $value ); break;
				case 'order.base.product.qtyopen': $item = $item->setQuantityOpen( (float) $value ); break;
				case 'order.base.product.notes': $item = $item->setNotes( (string) $value ); break;
				case 'order.base.product.statusdelivery': $item = $item->setStatusDelivery( is_numeric( $value ) ? (int) $value : null ); break;
				case 'order.base.product.statuspayment': $item = $item->setStatusPayment( is_numeric( $value ) ? (int) $value : null ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as associative list.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list['order.base.product.type'] = $this->getType();
		$list['order.base.product.stocktype'] = $this->getStockType();
		$list['order.base.product.prodcode'] = $this->getProductCode();
		$list['order.base.product.productid'] = $this->getProductId();
		$list['order.base.product.parentproductid'] = $this->getParentProductId();
		$list['order.base.product.supplierid'] = $this->getSupplierId();
		$list['order.base.product.suppliername'] = $this->getSupplierName();
		$list['order.base.product.qtyopen'] = $this->getQuantityOpen();
		$list['order.base.product.quantity'] = $this->getQuantity();
		$list['order.base.product.name'] = $this->getName();
		$list['order.base.product.description'] = $this->getDescription();
		$list['order.base.product.mediaurl'] = $this->getMediaUrl();
		$list['order.base.product.timeframe'] = $this->getTimeFrame();
		$list['order.base.product.position'] = $this->getPosition();
		$list['order.base.product.notes'] = $this->getNotes();
		$list['order.base.product.statuspayment'] = $this->getStatusPayment();
		$list['order.base.product.statusdelivery'] = $this->getStatusDelivery();

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
	 * @return bool True if the item properties are equal, false if not
	 * @since 2014.09
	 */
	public function compare( \Aimeos\MShop\Order\Item\Base\Product\Iface $item ) : bool
	{
		if( $this->getFlags() === $item->getFlags()
			&& $this->getName() === $item->getName()
			&& $this->getSiteId() === $item->getSiteId()
			&& $this->getStockType() === $item->getStockType()
			&& $this->getSupplierId() === $item->getSupplierId()
			&& $this->getProductCode() === $item->getProductCode()
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
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order product item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Product\Item\Iface $product ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		$values = $product->toArray();
		$this->fromArray( $values );

		$this->setSiteId( $product->getSiteId() );
		$this->setProductCode( $product->getCode() );
		$this->setProductId( $product->getId() );
		$this->setType( $product->getType() );
		$this->setTarget( $product->getTarget() );
		$this->setName( $product->getName() );

		if( ( $item = $product->getRefItems( 'text', 'basket', 'default' )->first() ) !== null ) {
			$this->setDescription( $item->getContent() );
		}

		if( ( $item = $product->getRefItems( 'media', 'default', 'default' )->first() ) !== null ) {
			$this->setMediaUrl( $item->getPreview() );
		}

		return $this->setModified();
	}
}
