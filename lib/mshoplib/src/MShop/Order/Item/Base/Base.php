<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base;


/**
 * Abstract order base class with necessary constants and basic methods.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class Base
	extends \Aimeos\MW\Observer\Publisher\Base
	implements \Aimeos\MShop\Order\Item\Base\Iface
{
	/**
	 * Check and load/store only basic basket content
	 */
	const PARTS_NONE = 0;

	/**
	 * Check and load/store basket with addresses
	 */
	const PARTS_ADDRESS = 1;

	/**
	 * Load/store basket with coupons
	 */
	const PARTS_COUPON = 2;

	/**
	 * Check and load/store basket with products
	 */
	const PARTS_PRODUCT = 4;

	/**
	 * Check and load/store basket with delivery/payment
	 */
	const PARTS_SERVICE = 8;

	/**
	 * Check and load/store basket with all parts.
	 */
	const PARTS_ALL = 15;


	// protected is a workaround for serialize problem
	protected $bdata;
	protected $coupons;
	protected $products;
	protected $services = [];
	protected $addresses = [];
	protected $modified = false;


	/**
	 * Initializes the basket object
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Default price of the basket (usually 0.00)
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Locale item containing the site, language and currency
	 * @param array $values Associative list of key/value pairs containing, e.g. the order or user ID
	 * @param array $products List of ordered products implementing \Aimeos\MShop\Order\Item\Base\Product\Iface
	 * @param array $addresses List of order addresses implementing \Aimeos\MShop\Order\Item\Base\Address\Iface
	 * @param array $services List of order services implementing \Aimeos\MShop\Order\Item\Base\Service\Iface
	 * @param array $coupons Associative list of coupon codes as keys and ordered products implementing \Aimeos\MShop\Order\Item\Base\Product\Iface as values
	 */
	public function __construct( \Aimeos\MShop\Price\Item\Iface $price, \Aimeos\MShop\Locale\Item\Iface $locale,
			array $values = [], array $products = [], array $addresses = [],
			array $services = [], array $coupons = [] )
	{
		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $addresses );
		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $products );
		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $services );

		foreach( $coupons as $couponProducts ) {
			\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $couponProducts );
		}

		$this->bdata = $values;
		$this->coupons = $coupons;
		$this->products = $products;

		foreach( $addresses as $address ) {
			$this->addresses[$address->getType()][] = $address;
		}

		foreach( $services as $service ) {
			$this->services[$service->getType()][$service->getServiceId()] = $service;
		}
	}


	/**
	 * Clones internal objects of the order base item.
	 */
	public function __clone()
	{
	}


	/**
	 * Returns the item property for the given name
	 *
	 * @param string $name Name of the property
	 * @return mixed|null Property value or null if property is unknown
	 */
	public function __get( $name )
	{
		if( isset( $this->bdata[$name] ) ) {
			return $this->bdata[$name];
		}
	}


	/**
	 * Tests if the item property for the given name is available
	 *
	 * @param string $name Name of the property
	 * @return boolean True if the property exists, false if not
	 */
	public function __isset( $name )
	{
		if( array_key_exists( $name, $this->bdata ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Prepares the object for serialization.
	 *
	 * @return array List of properties that should be serialized
	 */
	public function __sleep()
	{
		/*
		 * Workaround because database connections can't be serialized
		 * Listeners will be reattached on wakeup by the order base manager
		 */
		$this->off();

		return array_keys( get_object_vars( $this ) );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'order/base';
	}


	/**
	 * Tests if the order object was modified.
	 *
	 * @return bool True if modified, false if not
	 */
	public function isModified()
	{
		return $this->modified;
	}


	/**
	 * Sets the modified flag of the object.
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function setModified()
	{
		$this->modified = true;
		return $this;
	}


	/**
	 * Adds the address of the given type to the basket
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Address\Iface $address Order address item for the given type
	 * @param string $type Address type, usually "billing" or "delivery"
	 * @param integer|null $position Position of the address in the list
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function addAddress( \Aimeos\MShop\Order\Item\Base\Address\Iface $address, $type, $position = null )
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
	 * @param string $type Address type defined in \Aimeos\MShop\Order\Item\Base\Address\Base
	 * @param integer|null $position Position of the address in the list
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function deleteAddress( $type, $position = null )
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
	 * @param integer|null $position Address position in list of addresses
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface[]|\Aimeos\MShop\Order\Item\Base\Address\Iface Order address item or list of
	 */
	public function getAddress( $type, $position = null )
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
	 * @return array Associative list of address items implementing
	 *  \Aimeos\MShop\Order\Item\Base\Address\Iface with "billing" or "delivery" as key
	 */
	public function getAddresses()
	{
		return $this->addresses;
	}


	/**
	 * Replaces all addresses in the current basket with the new ones
	 *
	 * @param array $map Associative list of order addresses as returned by getAddresses()
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function setAddresses( array $map )
	{
		$map = $this->notify( 'setAddresses.before', $map );

		foreach( $map as $type => $items ) {
			$this->checkAddresses( $items, $type );
		}

		$old = $this->addresses;
		$this->addresses = $map;
		$this->setModified();

		$this->notify( 'setAddresses.after', $old );

		return $this;
	}


	/**
	 * Adds a coupon code and the given product item to the basket
	 *
	 * @param string $code Coupon code
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function addCoupon( $code )
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
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function deleteCoupon( $code )
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
	 * @return array Associative array of codes and lists of product items
	 *  implementing \Aimeos\MShop\Order\Product\Iface
	 */
	public function getCoupons()
	{
		return $this->coupons;
	}


	/**
	 * Sets a coupon code and the given product items in the basket.
	 *
	 * @param string $code Coupon code
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $products List of coupon products
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function setCoupon( $code, array $products = [] )
	{
		$new = $this->notify( 'setCoupon.before', [$code => $products] );

		$products = $this->checkProducts( current( $new ) );
		$code = key( $new );

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
		$this->coupons[$code] = $products;
		$this->setModified();

		$this->notify( 'setCoupon.after', $old );

		return $this;
	}


	/**
	 * Replaces all coupons in the current basket with the new ones
	 *
	 * @param array $map Associative list of order coupons as returned by getCoupons()
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function setCoupons( array $map )
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
		$this->coupons = $map;
		$this->setModified();

		$this->notify( 'setCoupons.after', $old );

		return $this;
	}


	/**
	 * Adds an order product item to the basket
	 * If a similar item is found, only the quantity is increased.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $item Order product item to be added
	 * @param integer|null $position position of the new order product item
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function addProduct( \Aimeos\MShop\Order\Item\Base\Product\Iface $item, $position = null )
	{
		$item = $this->notify( 'addProduct.before', $item );

		$this->checkProducts( [$item] );

		if( $position !== null ) {
			$this->products[$position] = $item;
		} elseif( ( $pos = $this->getSameProduct( $item, $this->products ) ) !== false ) {
			$this->products[$pos]->setQuantity( $this->products[$pos]->getQuantity() + $item->getQuantity() );
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
	 * @param integer $position Position of the order product item
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function deleteProduct( $position )
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
	 * @param integer $key Key returned by getProducts() identifying the requested product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Product item of an order
	 */
	public function getProduct( $key )
	{
		if( !isset( $this->products[$key] ) ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Product not available' ) );
		}

		return $this->products[$key];
	}


	/**
	 * Returns the product items that are or should be part of a basket
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface[] List of order product items
	 */
	public function getProducts()
	{
		return $this->products;
	}


	/**
	 * Replaces all products in the current basket with the new ones
	 *
	 * @param array $map Associative list of ordered products as returned by getProducts()
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function setProducts( array $map )
	{
		$map = $this->notify( 'setProducts.before', $map );

		$this->checkProducts( $map );

		$old = $this->products;
		$this->products = $map;
		$this->setModified();

		$this->notify( 'setProducts.after', $old );

		return $this;
	}


	/**
	 * Adds an order service to the basket
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Iface $service Order service item for the given domain
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
	 * @param integer|null $position Position of the address in the list to overwrite
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function addService( \Aimeos\MShop\Order\Item\Base\Service\Iface $service, $type, $position = null )
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
	 * @param integer|null $position Position of the address in the list to overwrite
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function deleteService( $type, $position = null )
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
	 * @param string|null $code Code of the service item that should be returned
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface[]|\Aimeos\MShop\Order\Item\Base\Service\Iface
	 * 	Order service item or list of items for the requested type
	 * @throws \Aimeos\MShop\Order\Exception If no service for the given type and code is found
	 */
	public function getService( $type, $code = null )
	{
		if( $code !== null )
		{
			if( isset( $this->services[$type] ) )
			{
				foreach( $this->services[$type] as $service )
				{
					if( $service->getCode() === $code ) {
						return $service;
					}
				}
			}

			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Service not available' ) );
		}

		return ( isset( $this->services[$type] ) ? $this->services[$type] : [] );
	}


	/**
	 * Returns all services that are part of the basket
	 *
	 * @return array Associative list of service types ("delivery" or "payment") as keys and list of
	 *	service items implementing \Aimeos\MShop\Order\Service\Iface as values
	 */
	public function getServices()
	{
		return $this->services;
	}


	/**
	 * Replaces all services in the current basket with the new ones
	 *
	 * @param array $map Associative list of order services as returned by getServices()
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function setServices( array $map )
	{
		$map = $this->notify( 'setServices.before', $map );

		foreach( $map as $type => $services ) {
			$map[$type] = $this->checkServices( $services, $type );
		}

		$old = $this->services;
		$this->services = $map;
		$this->setModified();

		$this->notify( 'setServices.after', $old );

		return $this;
	}


	/**
	 * Checks if the price uses the same currency as the price in the basket.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $item Price item
	 * @return null
	 */
	abstract protected function checkPrice( \Aimeos\MShop\Price\Item\Iface $item );


	/**
	 * Checks if all order addresses are valid
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Address\Iface[] $items Order address items
	 * @param string $type Address type constant from \Aimeos\MShop\Order\Item\Base\Address\Base
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface[] List of checked items
	 * @throws \Aimeos\MShop\Exception If one of the order addresses is invalid
	 */
	protected function checkAddresses( array $items, $type )
	{
		foreach( $items as $key => $item )
		{
			\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $item );
			$items[$key] = $item->setType( $type )->setId( null ); // enforce that the type and saving as new item
		}

		return $items;
	}


	/**
	 * Checks if all order products are valid
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $items Order product items
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface[] List of checked items
	 * @throws \Aimeos\MShop\Exception If one of the order products is invalid
	 */
	protected function checkProducts( array $items )
	{
		foreach( $items as $key => $item )
		{
			\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $item );

			if( $item->getProductCode() === '' ) {
				throw new \Aimeos\MShop\Order\Exception( sprintf( 'Product does not contain the SKU code' ) );
			}

			$this->checkPrice( $item->getPrice() );
			$items[$key] = $item->setId( null ); // enforce saving as new item
		}

		return $items;
	}


	/**
	 * Checks if all order services are valid
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Iface[] $items Order service items
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Base\Service\Base
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface[] List of checked items
	 * @throws \Aimeos\MShop\Exception If one of the order services is invalid
	 */
	protected function checkServices( array $items, $type )
	{
		$list = [];

		foreach( $items as $key => $item )
		{
			\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $item );

			$this->checkPrice( $item->getPrice() );
			$list[$key] = $item->setType( $type )->setId( null ); // enforce the type and saving as new item
		}

		return $list;
	}


	/**
	 * Tests if the given product is similar to an existing one.
	 * Similarity is described by the equality of properties so the quantity of
	 * the existing product can be updated.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $item Order product item
	 * @param array $products List of order product items to check against
	 * @return integer|false Positon of the same product in the product list of false if product is unique
	 * @throws \Aimeos\MShop\Order\Exception If no similar item was found
	 */
	protected function getSameProduct( \Aimeos\MShop\Order\Item\Base\Product\Iface $item, array $products )
	{
		$attributeMap = [];

		foreach( $item->getAttributeItems() as $attributeItem ) {
			$attributeMap[$attributeItem->getCode()] = $attributeItem;
		}

		foreach( $products as $position => $product )
		{
			if( $product->compare( $item ) === false ) {
				continue;
			}

			$prodAttributes = $product->getAttributeItems();

			if( count( $prodAttributes ) !== count( $attributeMap ) ) {
				continue;
			}

			foreach( $prodAttributes as $attribute )
			{
				if( array_key_exists( $attribute->getCode(), $attributeMap ) === false
					|| $attributeMap[$attribute->getCode()]->getValue() != $attribute->getValue()
					|| $attributeMap[$attribute->getCode()]->getQuantity() != $attribute->getQuantity()
				) {
					continue 2; // jump to outer loop
				}
			}

			return $position;
		}

		return false;
	}
}
