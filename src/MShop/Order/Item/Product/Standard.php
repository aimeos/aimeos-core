<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Product;


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
	 * @param \Aimeos\MShop\Order\Item\Product\Attribute\Iface[] $attributes List of order product attribute items
	 * @param \Aimeos\MShop\Order\Item\Product\Iface[] $products List of ordered subproduct items
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
		return $this->get( 'order.product.siteid', '' );
	}


	/**
	 * Sets the site ID of the item.
	 *
	 * @param string $value Unique site ID of the item
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setSiteId( string $value ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.siteid', $value );
	}


	/**
	 * Returns the base ID.
	 *
	 * @return string|null Base ID
	 */
	public function getParentId() : ?string
	{
		return $this->get( 'order.product.parentid' );
	}


	/**
	 * Sets the base order ID the product belongs to.
	 *
	 * @param string|null $value New order base ID
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setParentId( ?string $value ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.parentid', $value );
	}


	/**
	 * Returns the order address ID the product should be shipped to
	 *
	 * @return string|null Order address ID
	 */
	public function getOrderAddressId() : ?string
	{
		return $this->get( 'order.product.orderaddressid' );
	}


	/**
	 * Sets the order address ID the product should be shipped to
	 *
	 * @param string|null $value Order address ID
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setOrderAddressId( ?string $value ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.orderaddressid', $value );
	}


	/**
	 * Returns the parent ID of the ordered product if there is one.
	 * This ID relates to another product of the same order and provides a relation for e.g. sub-products in bundles.
	 *
	 * @return string|null Order product ID
	 */
	public function getOrderProductId() : ?string
	{
		return $this->get( 'order.product.orderproductid' );
	}


	/**
	 * Sets the parent ID of the ordered product.
	 * This ID relates to another product of the same order and provides a relation for e.g. sub-products in bundles.
	 *
	 * @param string|null $value Order product ID
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setOrderProductId( ?string $value ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.orderproductid', $value );
	}


	/**
	 * Returns the type of the ordered product.
	 *
	 * @return string Type of the ordered product
	 */
	public function getType() : string
	{
		return $this->get( 'order.product.type', '' );
	}


	/**
	 * Sets the type of the ordered product.
	 *
	 * @param string $type Type of the order product
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'order.product.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns the vendor.
	 *
	 * @return string Vendor name
	 */
	public function getVendor() : string
	{
		return $this->get( 'order.product.vendor', '' );
	}


	/**
	 * Sets the vendor.
	 *
	 * @param string|null $value Vendor name
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setVendor( ?string $value ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.vendor', (string) $value );
	}


	/**
	 * Returns the product ID the customer has selected.
	 *
	 * @return string Original product ID
	 */
	public function getProductId() : string
	{
		return $this->get( 'order.product.productid', '' );
	}


	/**
	 * Sets the ID of a product the customer has selected.
	 *
	 * @param string|null $id Product Code ID
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setProductId( ?string $id ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.productid', (string) $id );
	}


	/**
	 * Returns the product ID of the parent product.
	 *
	 * @return string Product ID of the parent product
	 */
	public function getParentProductId() : string
	{
		return $this->get( 'order.product.parentproductid', '' );
	}


	/**
	 * Sets the ID of the parent product the customer has selected.
	 *
	 * @param string|null $id Product ID of the parent product
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setParentProductId( ?string $id ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.parentproductid', (string) $id );
	}


	/**
	 * Returns the product code the customer has selected.
	 *
	 * @return string Product code
	 */
	public function getProductCode() : string
	{
		return $this->get( 'order.product.prodcode', '' );
	}


	/**
	 * Sets the code of a product the customer has selected.
	 *
	 * @param string $code Product code
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setProductCode( string $code ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.prodcode', $this->checkCode( $code ) );
	}


	/**
	 * Returns the code of the stock type the product should be retrieved from.
	 *
	 * @return string Stock type
	 */
	public function getStockType() : string
	{
		return $this->get( 'order.product.stocktype', '' );
	}


	/**
	 * Sets the code of the stock type the product should be retrieved from.
	 *
	 * @param string|null $code Stock type
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setStockType( ?string $code ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.stocktype', $this->checkCode( (string) $code ) );
	}


	/**
	 * Returns the localized name of the product.
	 *
	 * @param string|null $type Type the name is used for, e.g. "url"
	 * @return string Returns the localized name of the product
	 */
	public function getName( string $type = null ) : string
	{
		if( $type === 'url' ) {
			return \Aimeos\Base\Str::slug( $this->get( 'order.product.name', '' ) ?: $this->getProductCode() );
		}

		return $this->get( 'order.product.name', '' );
	}


	/**
	 * Sets the localized name of the product.
	 *
	 * @param string|null $value Localized name of the product
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setName( ?string $value ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.name', (string) $value );
	}


	/**
	 * Returns the localized description of the product.
	 *
	 * @return string Returns the localized description of the product
	 */
	public function getDescription() : string
	{
		return $this->get( 'order.product.description', '' );
	}


	/**
	 * Sets the localized description of the product.
	 *
	 * @param string|null $value Localized description of the product
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setDescription( ?string $value ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.description', (string) $value );
	}


	/**
	 * Returns the location of the media.
	 *
	 * @return string Location of the media
	 */
	public function getMediaUrl() : string
	{
		return $this->get( 'order.product.mediaurl', '' );
	}


	/**
	 * Sets the media url of the product the customer has added.
	 *
	 * @param string|null $value Location of the media/picture
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setMediaUrl( ?string $value ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.mediaurl', (string) $value );
	}


	/**
	 * Returns the URL target specific for that product
	 *
	 * @return string URL target specific for that product
	 */
	public function getTarget() : string
	{
		return $this->get( 'order.product.target', '' );
	}


	/**
	 * Sets the URL target specific for that product
	 *
	 * @param string|null $value New URL target specific for that product
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setTarget( ?string $value ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.target', (string) $value );
	}


	/**
	 * Returns the expected delivery time frame
	 *
	 * @return string Expected delivery time frame
	 */
	public function getTimeframe() : string
	{
		return $this->get( 'order.product.timeframe', '' );
	}


	/**
	 * Sets the expected delivery time frame
	 *
	 * @param string|null $timeframe Expected delivery time frame
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setTimeframe( ?string $timeframe ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.timeframe', (string) $timeframe );
	}


	/**
	 * Returns the amount of products the customer has added.
	 *
	 * @return float Amount of products
	 */
	public function getQuantity() : float
	{
		return (float) $this->get( 'order.product.quantity', 1 );
	}


	/**
	 * Sets the amount of products the customer has added.
	 *
	 * @param float $quantity Amount of products
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setQuantity( float $quantity ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		if( $quantity <= 0 || $quantity > 2147483647 ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Quantity must be greater than 0 and must not exceed 2147483647' ) );
		}

		return $this->set( 'order.product.quantity', $quantity );
	}


	/**
	 * Returns the number of packages not yet delivered to the customer.
	 *
	 * @return float Amount of product packages
	 */
	public function getQuantityOpen() : float
	{
		return (float) $this->get( 'order.product.qtyopen', $this->getQuantity() );
	}


	/**
	 * Sets the number of product packages not yet delivered to the customer.
	 *
	 * @param float $quantity Amount of product packages
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setQuantityOpen( float $quantity ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		if( $quantity < 0 || $quantity > $this->getQuantity() ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Quantity must be 0 or greater and must not exceed ordered quantity' ) );
		}

		return $this->set( 'order.product.qtyopen', $quantity );
	}


	/**
	 * Returns the quantity scale of the product.
	 *
	 * @return float Minimum quantity value
	 */
	public function getScale() : float
	{
		return (float) $this->get( 'order.product.scale', 1 );
	}


	/**
	 * Sets the quantity scale of the product.
	 *
	 * @param float $quantity Minimum quantity value
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setScale( float $quantity ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		if( $quantity <= 0 ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Quantity scale must be greater than 0' ) );
		}

		return $this->set( 'order.product.scale', $quantity );
	}


	/**
	 * 	Returns the flags for the product item.
	 *
	 * @return int Flags, e.g. for immutable products
	 */
	public function getFlags() : int
	{
		return $this->get( 'order.product.flags', \Aimeos\MShop\Order\Item\Product\Base::FLAG_NONE );
	}


	/**
	 * Sets the new value for the product item flags.
	 *
	 * @param int $value Flags, e.g. for immutable products
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setFlags( int $value ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.flags', $this->checkFlags( $value ) );
	}


	/**
	 * Returns the position of the product in the order.
	 *
	 * @return int|null Product position in the order from 0-n
	 */
	public function getPosition() : ?int
	{
		return $this->get( 'order.product.position' );
	}


	/**
	 * Sets the position of the product within the list of ordered products.
	 *
	 * @param int|null $value Product position in the order from 0-n or null for resetting the position
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 * @throws \Aimeos\MShop\Order\Exception If the position is invalid
	 */
	public function setPosition( ?int $value ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		if( $value < 0 ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Order product position "%1$s" must be greater than 0', $value ) );
		}

		return $this->set( 'order.product.position', $value );
	}


	/**
	 * Returns the current delivery status of the order product item.
	 *
	 * The returned status values are the STAT_* constants from the \Aimeos\MShop\Order\Item\Base class
	 *
	 * @return int Delivery status of the product
	 */
	public function getStatusDelivery() : int
	{
		return $this->get( 'order.product.statusdelivery', -1 );
	}


	/**
	 * Sets the new delivery status of the order product item.
	 *
	 * Possible status values are the STAT_* constants from the \Aimeos\MShop\Order\Item\Base class
	 *
	 * @param int $value New delivery status of the product
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setStatusDelivery( int $value ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.statusdelivery', $value );
	}


	/**
	 * Returns the current payment status of the order product item.
	 *
	 * The returned status values are the PAY_* constants from the \Aimeos\MShop\Order\Item\Base class
	 *
	 * @return int Payment status of the product
	 */
	public function getStatusPayment() : int
	{
		return $this->get( 'order.product.statuspayment', -1 );
	}


	/**
	 * Sets the new payment status of the order product item.
	 *
	 * Possible status values are the PAY_* constants from the \Aimeos\MShop\Order\Item\Base class
	 *
	 * @param int $value New payment status of the product
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setStatusPayment( int $value ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.statuspayment', $value );
	}


	/**
	 * Returns the notes for the ordered product.
	 *
	 * @return string Notes for the ordered product
	 */
	public function getNotes() : string
	{
		return $this->get( 'order.product.notes', '' );
	}


	/**
	 * Sets the notes for the ordered product.
	 *
	 * @param string|null $value Notes for the ordered product
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setNotes( ?string $value ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->set( 'order.product.notes', (string) $value );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order product item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$price = $this->getPrice();
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.product.siteid': !$private ?: $item = $item->setSiteId( $value ); break;
				case 'order.product.parentid': !$private ?: $item = $item->setParentId( $value ); break;
				case 'order.product.orderproductid': !$private ?: $item = $item->setOrderProductId( $value ); break;
				case 'order.product.orderaddressid': !$private ?: $item = $item->setOrderAddressId( $value ); break;
				case 'order.product.position': !$private ?: $item = $item->setPosition( (int) $value ); break;
				case 'order.product.flags': !$private ?: $item = $item->setFlags( (int) $value ); break;
				case 'order.product.target': !$private ?: $item = $item->setTarget( $value ); break;
				case 'order.product.parentproductid': $item = $item->setParentProductId( $value ); break;
				case 'order.product.productid': $item = $item->setProductId( $value ); break;
				case 'order.product.prodcode': $item = $item->setProductCode( $value ); break;
				case 'order.product.vendor': $item = $item->setVendor( $value ); break;
				case 'order.product.stocktype': $item = $item->setStockType( $value ); break;
				case 'order.product.type': $item = $item->setType( $value ); break;
				case 'order.product.currencyid': $price = $price->setCurrencyId( $value ); break;
				case 'order.product.price': $price = $price->setValue( $value ); break;
				case 'order.product.costs': $price = $price->setCosts( $value ); break;
				case 'order.product.rebate': $price = $price->setRebate( $value ); break;
				case 'order.product.taxrates': $price = $price->setTaxRates( $value ); break;
				case 'order.product.taxvalue': $price = $price->setTaxValue( $value ); break;
				case 'order.product.taxflag': $price = $price->setTaxFlag( $value ); break;
				case 'order.product.name': $item = $item->setName( $value ); break;
				case 'order.product.description': $item = $item->setDescription( $value ); break;
				case 'order.product.mediaurl': $item = $item->setMediaUrl( $value ); break;
				case 'order.product.timeframe': $item = $item->setTimeFrame( $value ); break;
				case 'order.product.scale': $item = $item->setScale( (float) $value ); break;
				case 'order.product.quantity': $item = $item->setQuantity( (float) $value ); break;
				case 'order.product.qtyopen': $item = $item->setQuantityOpen( (float) $value ); break;
				case 'order.product.notes': $item = $item->setNotes( (string) $value ); break;
				case 'order.product.statusdelivery': $item = $item->setStatusDelivery( (int) $value ); break;
				case 'order.product.statuspayment': $item = $item->setStatusPayment( (int) $value ); break;
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
		$price = $this->getPrice();
		$list = parent::toArray( $private );

		$list['order.product.type'] = $this->getType();
		$list['order.product.stocktype'] = $this->getStockType();
		$list['order.product.prodcode'] = $this->getProductCode();
		$list['order.product.productid'] = $this->getProductId();
		$list['order.product.parentproductid'] = $this->getParentProductId();
		$list['order.product.vendor'] = $this->getVendor();
		$list['order.product.scale'] = $this->getScale();
		$list['order.product.quantity'] = $this->getQuantity();
		$list['order.product.qtyopen'] = $this->getQuantityOpen();
		$list['order.product.currencyid'] = $price->getCurrencyId();
		$list['order.product.price'] = $price->getValue();
		$list['order.product.costs'] = $price->getCosts();
		$list['order.product.rebate'] = $price->getRebate();
		$list['order.product.taxrates'] = $price->getTaxRates();
		$list['order.product.taxvalue'] = $price->getTaxValue();
		$list['order.product.taxflag'] = $price->getTaxFlag();
		$list['order.product.name'] = $this->getName();
		$list['order.product.description'] = $this->getDescription();
		$list['order.product.mediaurl'] = $this->getMediaUrl();
		$list['order.product.timeframe'] = $this->getTimeFrame();
		$list['order.product.position'] = $this->getPosition();
		$list['order.product.notes'] = $this->getNotes();
		$list['order.product.statuspayment'] = $this->getStatusPayment();
		$list['order.product.statusdelivery'] = $this->getStatusDelivery();

		if( $private === true )
		{
			$list['order.product.parentid'] = $this->getParentId();
			$list['order.product.orderproductid'] = $this->getOrderProductId();
			$list['order.product.orderaddressid'] = $this->getOrderAddressId();
			$list['order.product.target'] = $this->getTarget();
			$list['order.product.flags'] = $this->getFlags();
		}

		return $list;
	}

	/**
	 * Compares the properties of the given order product item with its own ones.
	 *
	 * @param \Aimeos\MShop\Order\Item\Product\Iface $item Order product item
	 * @return bool True if the item properties are equal, false if not
	 * @since 2014.09
	 */
	public function compare( \Aimeos\MShop\Order\Item\Product\Iface $item ) : bool
	{
		if( $this->getFlags() === $item->getFlags()
			&& $this->getName() === $item->getName()
			&& $this->getSiteId() === $item->getSiteId()
			&& $this->getStockType() === $item->getStockType()
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
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order product item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Product\Item\Iface $product ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		if( $fcn = self::macro( 'copyFrom' ) ) {
			return $fcn( $product );
		}

		$values = $product->toArray();
		$this->fromArray( $values );

		$this->setSiteId( $product->getSiteId() );
		$this->setProductCode( $product->getCode() );
		$this->setProductId( $product->getId() );
		$this->setType( $product->getType() );
		$this->setScale( $product->getScale() );
		$this->setTarget( $product->getTarget() );
		$this->setName( $product->getName() );

		if( $item = $product->getRefItems( 'text', 'basket', 'default' )->first() ) {
			$this->setDescription( $item->getContent() );
		}

		if( $item = $product->getRefItems( 'media', 'default', 'default' )->first() ) {
			$this->setMediaUrl( $item->getPreview() );
		}

		if( $item = $product->getSiteItem() ) {
			$this->setVendor( $item->getLabel() );
		}

		return $this->setModified();
	}
}
