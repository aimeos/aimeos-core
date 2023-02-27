<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item;


/**
 * Base order item class with common constants and methods.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class Base
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Order\Item\Iface, \Aimeos\MW\Observer\Publisher\Iface,
		\Aimeos\Macro\Iface, \ArrayAccess, \JsonSerializable
{
	use \Aimeos\MW\Observer\Publisher\Traits;
	use \Aimeos\Macro\Macroable;


	/**
	 * Unfinished delivery.
	 * This is the default status after creating an order and this status
	 * should be also used as long as technical errors occurs.
	 */
	const STAT_UNFINISHED = -1;

	/**
	 * Delivery was deleted.
	 * The delivery of the order was deleted manually.
	 */
	const STAT_DELETED = 0;

	/**
	 * Delivery is pending.
	 * The order is not yet in the fulfillment process until further actions
	 * are taken.
	 */
	const STAT_PENDING = 1;

	/**
	 * Fulfillment in progress.
	 * The delivery of the order is in the (internal) fulfillment process and
	 * will be ready soon.
	 */
	const STAT_PROGRESS = 2;

	/**
	 * Parcel is dispatched.
	 * The parcel was given to the logistic partner for delivery to the
	 * customer.
	 */
	const STAT_DISPATCHED = 3;

	/**
	 * Parcel was delivered.
	 * The logistic partner delivered the parcel and the customer received it.
	 */
	const STAT_DELIVERED = 4;

	/**
	 * Parcel is lost.
	 * The parcel is lost during delivery by the logistic partner and haven't
	 * reached the customer nor it's returned to the merchant.
	 */
	const STAT_LOST = 5;

	/**
	 * Parcel was refused.
	 * The delivery of the parcel failed because the customer has refused to
	 * accept it or the address was invalid.
	 */
	const STAT_REFUSED = 6;

	/**
	 * Parcel was returned.
	 * The parcel was sent back by the customer.
	 */
	const STAT_RETURNED = 7;


	/**
	 * Unfinished payment.
	 * This is the default status after creating an order and this status
	 * should be also used as long as technical errors occurs.
	 */
	const PAY_UNFINISHED = -1;

	/**
	 * Payment was deleted.
	 * The payment for the order was deleted manually.
	 */
	const PAY_DELETED = 0;

	/**
	 * Payment was canceled.
	 * The customer canceled the payment process.
	 */
	const PAY_CANCELED = 1;

	/**
	 * Payment was refused.
	 * The customer didn't enter valid payment details.
	 */
	const PAY_REFUSED = 2;

	/**
	 * Payment was refund.
	 * The payment was OK but refund and the customer got his money back.
	 */
	const PAY_REFUND = 3;

	/**
	 * Payment is pending.
	 * The payment is not yet done until further actions are taken.
	 */
	const PAY_PENDING = 4;

	/**
	 * Payment is authorized.
	 * The customer authorized the merchant to invoice the amount but the money
	 * is not yet received. This is used for all post-paid orders.
	 */
	const PAY_AUTHORIZED = 5;

	/**
	 * Payment is received.
	 * The merchant received the money from the customer.
	 */
	const PAY_RECEIVED = 6;

	/**
	 * Payment is transferred.
	 * The vendor received the money from the platform.
	 */
	const PAY_TRANSFERRED = 7;


	// protected is a workaround for serialize problem
	protected array $coupons;
	protected array $products;
	protected array $services = [];
	protected array $addresses = [];


	/**
	 * Initializes the basket object
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Default price of the basket (usually 0.00)
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Locale item containing the site, language and currency
	 * @param array $values Associative list of key/value pairs containing, e.g. the order or user ID
	 * @param array $products List of ordered products implementing \Aimeos\MShop\Order\Item\Product\Iface
	 * @param array $addresses List of order addresses implementing \Aimeos\MShop\Order\Item\Address\Iface
	 * @param array $services List of order services implementing \Aimeos\MShop\Order\Item\Service\Iface
	 * @param array $coupons Associative list of coupon codes as keys and ordered products implementing \Aimeos\MShop\Order\Item\Product\Iface as values
	 */
	public function __construct( \Aimeos\MShop\Price\Item\Iface $price, \Aimeos\MShop\Locale\Item\Iface $locale,
		array $values = [], array $products = [], array $addresses = [],
		array $services = [], array $coupons = [] )
	{
		map( $addresses )->implements( \Aimeos\MShop\Order\Item\Address\Iface::class, true );
		map( $products )->implements( \Aimeos\MShop\Order\Item\Product\Iface::class, true );
		map( $services )->implements( \Aimeos\MShop\Order\Item\Service\Iface::class, true );

		foreach( $coupons as $couponProducts ) {
			map( $couponProducts )->implements( \Aimeos\MShop\Order\Item\Product\Iface::class, true );
		}

		parent::__construct( 'order.', $values );

		$this->coupons = $coupons;
		$this->products = $products;

		foreach( $addresses as $address ) {
			$this->addresses[$address->getType()][] = $address;
		}

		foreach( $services as $service ) {
			$this->services[$service->getType()][] = $service;
		}
	}


	/**
	 * Specifies the data which should be serialized to JSON by json_encode().
	 *
	 * @return array<string,mixed> Data to serialize to JSON
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize()
	{
		return parent::jsonSerialize() + [
			'coupons' => $this->coupons,
			'products' => $this->products,
			'services' => $this->services,
			'addresses' => $this->addresses,
		];
	}


	/**
	 * Prepares the object for serialization.
	 *
	 * @return array List of properties that should be serialized
	 */
	public function __sleep() : array
	{
		/*
		 * Workaround because database connections can't be serialized
		 * Listeners will be reattached on wakeup by the order base manager
		 */
		$this->off();

		return array_keys( get_object_vars( $this ) );
	}


	/**
	 * Returns the ID of the items
	 *
	 * @return string ID of the item or null
	 */
	public function __toString() : string
	{
		return (string) $this->getId();
	}


	/**
	 * Tests if all necessary items are available to create the order.
	 *
	 * @param array $what Type of data
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
	 * @throws \Aimeos\MShop\Order\Exception if there are no products in the basket
	 */
	public function check( array $what = ['order/address', 'order/coupon', 'order/product', 'order/service'] ) : \Aimeos\MShop\Order\Item\Iface
	{
		$this->notify( 'check.before', $what );

		if( in_array( 'order/product', $what ) && ( count( $this->getProducts() ) < 1 ) ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Basket empty' ) );
		}

		$this->notify( 'check.after', $what );

		return $this;
	}


	/**
	 * Notifies listeners before the basket becomes an order.
	 *
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for chaining method calls
	 */
	public function finish() : \Aimeos\MShop\Order\Item\Iface
	{
		$this->notify( 'setOrder.before' );
		return $this;
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'order';
	}


	/**
	 * Adds the address of the given type to the basket
	 *
	 * @param \Aimeos\MShop\Order\Item\Address\Iface $address Order address item for the given type
	 * @param string $type Address type, usually "billing" or "delivery"
	 * @param int|null $position Position of the address in the list
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
	 */
	public function addAddress( \Aimeos\MShop\Order\Item\Address\Iface $address, string $type, int $position = null ) : \Aimeos\MShop\Order\Item\Iface
	{
		$address = $this->notify( 'addAddress.before', $address );

		$address = clone $address;
		$address = $address->setType( $type );

		if( $position !== null ) {
			$this->addresses[$type][$position] = $address;
		} else {
			$this->addresses[$type][] = $address;
		}

		$this->setModified();

		$this->notify( 'addAddress.after', $address );

		return $this;
	}


	/**
	 * Deletes an order address from the basket
	 *
	 * @param string $type Address type defined in \Aimeos\MShop\Order\Item\Address\Base
	 * @param int|null $position Position of the address in the list
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
	 */
	public function deleteAddress( string $type, int $position = null ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( $position === null && isset( $this->addresses[$type] ) || isset( $this->addresses[$type][$position] ) )
		{
			$old = ( isset( $this->addresses[$type][$position] ) ? $this->addresses[$type][$position] : $this->addresses[$type] );
			$old = $this->notify( 'deleteAddress.before', $old );

			if( $position !== null ) {
				unset( $this->addresses[$type][$position] );
			} else {
				unset( $this->addresses[$type] );
			}

			$this->setModified();

			$this->notify( 'deleteAddress.after', $old );
		}

		return $this;
	}


	/**
	 * Returns the order address depending on the given type
	 *
	 * @param string $type Address type, usually "billing" or "delivery"
	 * @param int|null $position Address position in list of addresses
	 * @return \Aimeos\MShop\Order\Item\Address\Iface[]|\Aimeos\MShop\Order\Item\Address\Iface Order address item or list of
	 */
	public function getAddress( string $type, int $position = null )
	{
		if( $position !== null )
		{
			if( isset( $this->addresses[$type][$position] ) ) {
				return $this->addresses[$type][$position];
			}

			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Address not available' ) );
		}

		return ( isset( $this->addresses[$type] ) ? $this->addresses[$type] : [] );
	}


	/**
	 * Returns all addresses that are part of the basket
	 *
	 * @return \Aimeos\Map Associative list of address items implementing
	 *  \Aimeos\MShop\Order\Item\Address\Iface with "billing" or "delivery" as key
	 */
	public function getAddresses() : \Aimeos\Map
	{
		return map( $this->addresses );
	}


	/**
	 * Replaces all addresses in the current basket with the new ones
	 *
	 * @param \Aimeos\Map|array $map Associative list of order addresses as returned by getAddresses()
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
	 */
	public function setAddresses( iterable $map ) : \Aimeos\MShop\Order\Item\Iface
	{
		$map = $this->notify( 'setAddresses.before', $map );

		foreach( $map as $type => $items ) {
			$this->checkAddresses( $items, $type );
		}

		$old = $this->addresses;
		$this->addresses = is_map( $map ) ? $map->toArray() : $map;
		$this->setModified();

		$this->notify( 'setAddresses.after', $old );

		return $this;
	}


	/**
	 * Adds a coupon code and the given product item to the basket
	 *
	 * @param string $code Coupon code
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
	 */
	public function addCoupon( string $code ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( !isset( $this->coupons[$code] ) )
		{
			$code = $this->notify( 'addCoupon.before', $code );

			$this->coupons[$code] = [];
			$this->setModified();

			$this->notify( 'addCoupon.after', $code );
		}

		return $this;
	}


	/**
	 * Removes a coupon and the related product items from the basket
	 *
	 * @param string $code Coupon code
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
	 */
	public function deleteCoupon( string $code ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( isset( $this->coupons[$code] ) )
		{
			$old = [$code => $this->coupons[$code]];
			$old = $this->notify( 'deleteCoupon.before', $old );

			foreach( $this->coupons[$code] as $product )
			{
				if( ( $key = array_search( $product, $this->products, true ) ) !== false ) {
					unset( $this->products[$key] );
				}
			}

			unset( $this->coupons[$code] );
			$this->setModified();

			$this->notify( 'deleteCoupon.after', $old );
		}

		return $this;
	}


	/**
	 * Returns the available coupon codes and the lists of affected product items
	 *
	 * @return \Aimeos\Map Associative array of codes and lists of product items
	 *  implementing \Aimeos\MShop\Order\Product\Iface
	 */
	public function getCoupons() : \Aimeos\Map
	{
		return map( $this->coupons );
	}


	/**
	 * Sets a coupon code and the given product items in the basket.
	 *
	 * @param string $code Coupon code
	 * @param \Aimeos\MShop\Order\Item\Product\Iface[] $products List of coupon products
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
	 */
	public function setCoupon( string $code, iterable $products = [] ) : \Aimeos\MShop\Order\Item\Iface
	{
		$new = $this->notify( 'setCoupon.before', [$code => $products] );

		$products = $this->checkProducts( map( $new )->first( [] ) );

		if( isset( $this->coupons[$code] ) )
		{
			foreach( $this->coupons[$code] as $product )
			{
				if( ( $key = array_search( $product, $this->products, true ) ) !== false ) {
					unset( $this->products[$key] );
				}
			}
		}

		foreach( $products as $product ) {
			$this->products[] = $product;
		}

		$old = isset( $this->coupons[$code] ) ? [$code => $this->coupons[$code]] : [];
		$this->coupons[$code] = is_map( $products ) ? $products->toArray() : $products;
		$this->setModified();

		$this->notify( 'setCoupon.after', $old );

		return $this;
	}


	/**
	 * Replaces all coupons in the current basket with the new ones
	 *
	 * @param iterable $map Associative list of order coupons as returned by getCoupons()
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
	 */
	public function setCoupons( iterable $map ) : \Aimeos\MShop\Order\Item\Iface
	{
		$map = $this->notify( 'setCoupons.before', $map );

		foreach( $map as $code => $products ) {
			$map[$code] = $this->checkProducts( $products );
		}

		foreach( $this->coupons as $code => $products )
		{
			foreach( $products as $product )
			{
				if( ( $key = array_search( $product, $this->products, true ) ) !== false ) {
					unset( $this->products[$key] );
				}
			}
		}

		foreach( $map as $code => $products )
		{
			foreach( $products as $product ) {
				$this->products[] = $product;
			}
		}

		$old = $this->coupons;
		$this->coupons = is_map( $map ) ? $map->toArray() : $map;
		$this->setModified();

		$this->notify( 'setCoupons.after', $old );

		return $this;
	}


	/**
	 * Adds an order product item to the basket
	 * If a similar item is found, only the quantity is increased.
	 *
	 * @param \Aimeos\MShop\Order\Item\Product\Iface $item Order product item to be added
	 * @param int|null $position position of the new order product item
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
	 */
	public function addProduct( \Aimeos\MShop\Order\Item\Product\Iface $item, int $position = null ) : \Aimeos\MShop\Order\Item\Iface
	{
		$item = $this->notify( 'addProduct.before', $item );

		$this->checkProducts( [$item] );

		if( $position !== null ) {
			$this->products[$position] = $item;
		} elseif( ( $pos = $this->getSameProduct( $item, $this->products ) ) !== null ) {
			$item = $this->products[$pos]->setQuantity( $this->products[$pos]->getQuantity() + $item->getQuantity() );
		} else {
			$this->products[] = $item;
		}

		ksort( $this->products );
		$this->setModified();

		$this->notify( 'addProduct.after', $item );

		return $this;
	}


	/**
	 * Deletes an order product item from the basket
	 *
	 * @param int $position Position of the order product item
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
	 */
	public function deleteProduct( int $position ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( isset( $this->products[$position] ) )
		{
			$old = $this->products[$position];
			$old = $this->notify( 'deleteProduct.before', $old );

			unset( $this->products[$position] );
			$this->setModified();

			$this->notify( 'deleteProduct.after', $old );
		}

		return $this;
	}


	/**
	 * Returns the product item of an basket specified by its key
	 *
	 * @param int $key Key returned by getProducts() identifying the requested product
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Product item of an order
	 */
	public function getProduct( int $key ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		if( !isset( $this->products[$key] ) ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Product not available' ) );
		}

		return $this->products[$key];
	}


	/**
	 * Returns the product items that are or should be part of a basket
	 *
	 * @return \Aimeos\Map List of order product items implementing \Aimeos\MShop\Order\Item\Product\Iface
	 */
	public function getProducts() : \Aimeos\Map
	{
		return map( $this->products );
	}


	/**
	 * Replaces all products in the current basket with the new ones
	 *
	 * @param \Aimeos\MShop\Order\Item\Product\Iface[] $map Associative list of ordered products as returned by getProducts()
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
	 */
	public function setProducts( iterable $map ) : \Aimeos\MShop\Order\Item\Iface
	{
		$map = $this->notify( 'setProducts.before', $map );

		$this->checkProducts( $map );

		$old = $this->products;
		$this->products = is_map( $map ) ? $map->toArray() : $map;
		$this->setModified();

		$this->notify( 'setProducts.after', $old );

		return $this;
	}


	/**
	 * Adds an order service to the basket
	 *
	 * @param \Aimeos\MShop\Order\Item\Service\Iface $service Order service item for the given domain
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
	 * @param int|null $position Position of the service in the list to overwrite
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
	 */
	public function addService( \Aimeos\MShop\Order\Item\Service\Iface $service, string $type, int $position = null ) : \Aimeos\MShop\Order\Item\Iface
	{
		$service = $this->notify( 'addService.before', $service );

		$this->checkPrice( $service->getPrice() );

		$service = clone $service;
		$service = $service->setType( $type );

		if( $position !== null ) {
			$this->services[$type][$position] = $service;
		} else {
			$this->services[$type][] = $service;
		}

		$this->setModified();

		$this->notify( 'addService.after', $service );

		return $this;
	}


	/**
	 * Deletes an order service from the basket
	 *
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
	 * @param int|null $position Position of the service in the list to delete
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
	 */
	public function deleteService( string $type, int $position = null ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( $position === null && isset( $this->services[$type] ) || isset( $this->services[$type][$position] ) )
		{
			$old = ( isset( $this->services[$type][$position] ) ? $this->services[$type][$position] : $this->services[$type] );
			$old = $this->notify( 'deleteService.before', $old );

			if( $position !== null ) {
				unset( $this->services[$type][$position] );
			} else {
				unset( $this->services[$type] );
			}

			$this->setModified();

			$this->notify( 'deleteService.after', $old );
		}

		return $this;
	}


	/**
	 * Returns the order services depending on the given type
	 *
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
	 * @param int|null $position Position of the service in the list to retrieve
	 * @return \Aimeos\MShop\Order\Item\Service\Iface[]|\Aimeos\MShop\Order\Item\Service\Iface
	 * 	Order service item or list of items for the requested type
	 * @throws \Aimeos\MShop\Order\Exception If no service for the given type and position is found
	 */
	public function getService( string $type, int $position = null )
	{
		if( $position !== null )
		{
			if( isset( $this->services[$type][$position] ) ) {
				return $this->services[$type][$position];
			}

			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Service not available' ) );
		}

		return ( isset( $this->services[$type] ) ? $this->services[$type] : [] );
	}


	/**
	 * Returns all services that are part of the basket
	 *
	 * @return \Aimeos\Map Associative list of service types ("delivery" or "payment") as keys and list of
	 *	service items implementing \Aimeos\MShop\Order\Service\Iface as values
	 */
	public function getServices() : \Aimeos\Map
	{
		return map( $this->services );
	}


	/**
	 * Replaces all services in the current basket with the new ones
	 *
	 * @param \Aimeos\MShop\Order\Item\Service\Iface[] $map Associative list of order services as returned by getServices()
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
	 */
	public function setServices( iterable $map ) : \Aimeos\MShop\Order\Item\Iface
	{
		$map = $this->notify( 'setServices.before', $map );

		foreach( $map as $type => $services ) {
			$map[$type] = $this->checkServices( $services, $type );
		}

		$old = $this->services;
		$this->services = is_map( $map ) ? $map->toArray() : $map;
		$this->setModified();

		$this->notify( 'setServices.after', $old );

		return $this;
	}


	/**
	 * Returns the service costs
	 *
	 * @param string $type Service type like "delivery" or "payment"
	 * @return float Service costs value
	 */
	public function getCosts( string $type = 'delivery' ) : float
	{
		$costs = 0;

		if( $type === 'delivery' )
		{
			foreach( $this->getProducts() as $product ) {
				$costs += $product->getPrice()->getCosts() * $product->getQuantity();
			}
		}

		foreach( $this->getService( $type ) as $service ) {
			$costs += $service->getPrice()->getCosts();
		}

		return $costs;
	}


	/**
	 * Returns a list of tax names and values
	 *
	 * @return array Associative list of tax names as key and price items as value
	 */
	public function getTaxes() : array
	{
		$taxes = [];

		foreach( $this->getProducts() as $product )
		{
			$price = $product->getPrice();

			foreach( $price->getTaxrates() as $name => $taxrate )
			{
				$price = (clone $price)->setTaxRate( $taxrate );

				if( isset( $taxes[$name][$taxrate] ) ) {
					$taxes[$name][$taxrate]->addItem( $price, $product->getQuantity() );
				} else {
					$taxes[$name][$taxrate] = $price->addItem( $price, $product->getQuantity() - 1 );
				}
			}
		}

		foreach( $this->getServices() as $services )
		{
			foreach( $services as $service )
			{
				$price = $service->getPrice();

				foreach( $price->getTaxrates() as $name => $taxrate )
				{
					$price = (clone $price)->setTaxRate( $taxrate );

					if( isset( $taxes[$name][$taxrate] ) ) {
						$taxes[$name][$taxrate]->addItem( $price );
					} else {
						$taxes[$name][$taxrate] = $price;
					}
				}
			}
		}

		return $taxes;
	}


	/**
	 * Checks if the price uses the same currency as the price in the basket.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $item Price item
	 */
	protected function checkPrice( \Aimeos\MShop\Price\Item\Iface $item )
	{
		$price = clone $this->getPrice();
		$price->addItem( $item );
	}


	/**
	 * Checks if the given delivery status is a valid constant.
	 *
	 * @param int $value Delivery status constant defined in \Aimeos\MShop\Order\Item\Base
	 * @return int Delivery status constant defined in \Aimeos\MShop\Order\Item\Base
	 * @throws \Aimeos\MShop\Order\Exception If delivery status is invalid
	 */
	protected function checkDeliveryStatus( int $value )
	{
		if( $value < \Aimeos\MShop\Order\Item\Base::STAT_UNFINISHED || $value > \Aimeos\MShop\Order\Item\Base::STAT_RETURNED ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Order delivery status "%1$s" not within allowed range', $value ) );
		}

		return $value;
	}


	/**
	 * Checks the given payment status is a valid constant.
	 *
	 * @param int $value Payment status constant defined in \Aimeos\MShop\Order\Item\Base
	 * @return int Payment status constant defined in \Aimeos\MShop\Order\Item\Base
	 * @throws \Aimeos\MShop\Order\Exception If payment status is invalid
	 */
	protected function checkPaymentStatus( int $value )
	{
		if( $value < \Aimeos\MShop\Order\Item\Base::PAY_UNFINISHED || $value > \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Order payment status "%1$s" not within allowed range', $value ) );
		}

		return $value;
	}


	/**
	 * Checks if all order addresses are valid
	 *
	 * @param \Aimeos\MShop\Order\Item\Address\Iface[] $items Order address items
	 * @param string $type Address type constant from \Aimeos\MShop\Order\Item\Address\Base
	 * @return \Aimeos\MShop\Order\Item\Address\Iface[] List of checked items
	 * @throws \Aimeos\MShop\Exception If one of the order addresses is invalid
	 */
	protected function checkAddresses( iterable $items, string $type ) : iterable
	{
		map( $items )->implements( \Aimeos\MShop\Order\Item\Address\Iface::class, true );

		foreach( $items as $key => $item ) {
			$items[$key] = $item->setType( $type );
		}

		return $items;
	}


	/**
	 * Checks if all order products are valid
	 *
	 * @param \Aimeos\MShop\Order\Item\Product\Iface[] $items Order product items
	 * @return \Aimeos\MShop\Order\Item\Product\Iface[] List of checked items
	 * @throws \Aimeos\MShop\Exception If one of the order products is invalid
	 */
	protected function checkProducts( iterable $items ) : \Aimeos\Map
	{
		map( $items )->implements( \Aimeos\MShop\Order\Item\Product\Iface::class, true );

		foreach( $items as $key => $item )
		{
			if( $item->getProductCode() === '' ) {
				throw new \Aimeos\MShop\Order\Exception( sprintf( 'Product does not contain the SKU code' ) );
			}

			$this->checkPrice( $item->getPrice() );
		}

		return map( $items );
	}


	/**
	 * Checks if all order services are valid
	 *
	 * @param \Aimeos\MShop\Order\Item\Service\Iface[] $items Order service items
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
	 * @return \Aimeos\MShop\Order\Item\Service\Iface[] List of checked items
	 * @throws \Aimeos\MShop\Exception If one of the order services is invalid
	 */
	protected function checkServices( iterable $items, string $type ) : iterable
	{
		map( $items )->implements( \Aimeos\MShop\Order\Item\Service\Iface::class, true );

		foreach( $items as $key => $item )
		{
			$this->checkPrice( $item->getPrice() );
			$items[$key] = $item->setType( $type );
		}

		return $items;
	}


	/**
	 * Tests if the given product is similar to an existing one.
	 * Similarity is described by the equality of properties so the quantity of
	 * the existing product can be updated.
	 *
	 * @param \Aimeos\MShop\Order\Item\Product\Iface $item Order product item
	 * @param \Aimeos\MShop\Order\Item\Product\Iface[] $products List of order product items to check against
	 * @return int|null Positon of the same product in the product list of false if product is unique
	 * @throws \Aimeos\MShop\Order\Exception If no similar item was found
	 */
	protected function getSameProduct( \Aimeos\MShop\Order\Item\Product\Iface $item, iterable $products ) : ?int
	{
		$map = [];
		$count = 0;

		foreach( $item->getAttributeItems() as $attributeItem )
		{
			$key = md5( $attributeItem->getCode() . json_encode( $attributeItem->getValue() ) );
			$map[$key] = $attributeItem;
			$count++;
		}

		foreach( $products as $position => $product )
		{
			if( $product->compare( $item ) === false ) {
				continue;
			}

			$prodAttributes = $product->getAttributeItems();

			if( count( $prodAttributes ) !== $count ) {
				continue;
			}

			foreach( $prodAttributes as $attribute )
			{
				$key = md5( $attribute->getCode() . json_encode( $attribute->getValue() ) );

				if( isset( $map[$key] ) === false || $map[$key]->getQuantity() != $attribute->getQuantity() ) {
					continue 2; // jump to outer loop
				}
			}

			return $position;
		}

		return null;
	}
}
