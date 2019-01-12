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
	protected $addresses;
	protected $services = [];
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
		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $products );
		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $addresses );
		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $services );

		foreach( $coupons as $couponProducts ) {
			\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $couponProducts );
		}

		$this->bdata = $values;
		$this->coupons = $coupons;
		$this->products = $products;
		$this->addresses = $addresses;

		foreach( $services as $service ) {
			$this->services[$service->getType()][$service->getServiceId()] = $service;
		}
	}


	/**
	 * Clones internal objects of the order base item.
	 */
	public function __clone()
	{
		foreach( $this->products as $key => $value ) {
			$this->products[$key] = $value;
		}

		foreach( $this->addresses as $key => $value ) {
			$this->addresses[$key] = $value;
		}

		foreach( $this->services as $key => $value ) {
			$this->services[$key] = $value;
		}

		foreach( $this->coupons as $key => $value ) {
			$this->coupons[$key] = $value;
		}
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
		$this->clearListeners();

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
	 * Adds an order product item to the basket
	 * If a similar item is found, only the quantity is increased.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $item Order product item to be added
	 * @param integer|null $position position of the new order product item
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function addProduct( \Aimeos\MShop\Order\Item\Base\Product\Iface $item, $position = null )
	{
		$this->checkProducts( [$item] );

		$this->notifyListeners( 'addProduct.before', $item );

		if( $position !== null ) {
			$this->products[$position] = $item;
		} elseif( ( $pos = $this->getSameProduct( $item, $this->products ) ) !== false ) {
			$this->products[$pos]->setQuantity( $this->products[$pos]->getQuantity() + $item->getQuantity() );
		} else {
			$this->products[] = $item;
		}

		ksort( $this->products );
		$this->setModified();

		$this->notifyListeners( 'addProduct.after', $item );

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
			$this->notifyListeners( 'deleteProduct.before', $position );

			$old = $this->products[$position];
			unset( $this->products[$position] );
			$this->setModified();

			$this->notifyListeners( 'deleteProduct.after', $old );
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
	 * @return array Array of order product items implementing \Aimeos\MShop\Order\Item\Base\Product\Iface
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
		$this->checkProducts( $map );

		$this->notifyListeners( 'setProducts.before', $map );

		$old = $this->products;
		$this->products = $map;
		$this->setModified();

		$this->notifyListeners( 'setProducts.after', $old );

		return $this;
	}


	/**
	 * Deletes an order address from the basket
	 *
	 * @param string $type Address type defined in \Aimeos\MShop\Order\Item\Base\Address\Base
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function deleteAddress( $type )
	{
		if( isset( $this->addresses[$type] ) )
		{
			$this->notifyListeners( 'deleteAddress.before', $type );

			$old = $this->addresses[$type];
			unset( $this->addresses[$type] );
			$this->setModified();

			$this->notifyListeners( 'deleteAddress.after', $old );
		}

		return $this;
	}


	/**
	 * Returns the order address depending on the given type
	 *
	 * @param string $type Address type, usually "billing" or "delivery"
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order address item for the requested type
	 */
	public function getAddress( $type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT )
	{
		if( !isset( $this->addresses[$type] ) ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Address not available' ) );
		}

		return $this->addresses[$type];
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
	 * Sets the address of the given type in the basket
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Address\Iface $address Order address item for the given type
	 * @param string $type Address type, usually "billing" or "delivery"
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function setAddress( \Aimeos\MShop\Order\Item\Base\Address\Iface $address, $type )
	{
		$this->notifyListeners( 'setAddress.before', $address );

		$address = clone $address;
		$address->setType( $type ); // enforce that the type is the same as the given one
		$address->setId( null ); // enforce saving as new item

		$this->addresses[$type] = $address;
		$this->setModified();

		$this->notifyListeners( 'setAddress.after', $address );

		return $this;
	}


	/**
	 * Replaces all addresses in the current basket with the new ones
	 *
	 * @param array $map Associative list of order addresses as returned by getAddresses()
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function setAddresses( array $map )
	{
		foreach( $map as $type => $item ) {
			$this->checkAddresses( [$item], $type );
		}

		$this->notifyListeners( 'setAddresses.before', $map );

		$old = $this->addresses;
		$this->addresses = $map;
		$this->setModified();

		$this->notifyListeners( 'setAddresses.after', $old );

		return $this;
	}


	/**
	 * Adds an order service to the basket
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Iface $service Order service item for the given domain
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function addService( \Aimeos\MShop\Order\Item\Base\Service\Iface $service, $type )
	{
		$this->checkPrice( $service->getPrice() );

		$this->notifyListeners( 'addService.before', $service );

		$service = clone $service;
		$service->setType( $type ); // enforce that the type is the same as the given one
		$service->setId( null ); // enforce saving as new item

		$this->services[$type][$service->getServiceId()] = $service;
		$this->setModified();

		$this->notifyListeners( 'addService.after', $service );

		return $this;
	}


	/**
	 * Deletes an order service from the basket
	 *
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function deleteService( $type )
	{
		if( isset( $this->services[$type] ) )
		{
			$this->notifyListeners( 'deleteService.before', $type );

			$old = $this->services[$type];
			unset( $this->services[$type] );
			$this->setModified();

			$this->notifyListeners( 'deleteService.after', $old );
		}

		return $this;
	}


	/**
	 * Returns the order services depending on the given type
	 *
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
	 * @param string|null $code Code of the service item that should be returned
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface|\Aimeos\MShop\Order\Item\Base\Service\Iface[]
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
		foreach( $map as $type => $services ) {
			$map[$type] = $this->checkServices( $services, $type );
		}

		$this->notifyListeners( 'setServices.before', $map );

		$old = $this->services;
		$this->services = $map;
		$this->setModified();

		$this->notifyListeners( 'setServices.after', $old );

		return $this;
	}


	/**
	 * Adds a coupon code and the given product item to the basket
	 *
	 * @param string $code Coupon code
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $products List of coupon products
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function addCoupon( $code, array $products = [] )
	{
		if( isset( $this->coupons[$code] ) ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Duplicate coupon code' ) );
		}

		$this->checkProducts( $products );

		$this->notifyListeners( 'addCoupon.before', $products );

		foreach( $products as $product ) {
			$this->products[] = $product;
		}

		$this->coupons[$code] = $products;
		$this->setModified();

		$this->notifyListeners( 'addCoupon.after', $code );

		return $this;
	}


	/**
	 * Removes a coupon and the related product items from the basket
	 *
	 * @param string $code Coupon code
	 * @param boolean $removecode If the coupon code should also be removed
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function deleteCoupon( $code, $removecode = false )
	{
		if( isset( $this->coupons[$code] ) )
		{
			$this->notifyListeners( 'deleteCoupon.before', $code );

			foreach( $this->coupons[$code] as $product )
			{
				if( ( $key = array_search( $product, $this->products, true ) ) !== false ) {
					unset( $this->products[$key] );
				}
			}

			$old = [$code => $this->coupons[$code]];

			if( $removecode === true ) {
				unset( $this->coupons[$code] );
			} else {
				$this->coupons[$code] = [];
			}

			$this->setModified();

			$this->notifyListeners( 'deleteCoupon.after', $old );
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
	 * Replaces all coupons in the current basket with the new ones
	 *
	 * @param array $map Associative list of order coupons as returned by getCoupons()
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 */
	public function setCoupons( array $map )
	{
		foreach( $map as $code => $products ) {
			$map[$code] = $this->checkProducts( $products );
		}

		$this->notifyListeners( 'setCoupons.before', $map );

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

		$this->notifyListeners( 'setCoupons.after', $old );

		return $this;
	}


	/**
	 * Tests if all necessary items are available to create the order.
	 *
	 * @param integer $what Test for the specific type of completeness
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 * @throws \Aimeos\MShop\Order\Exception if there are no products in the basket
	 */
	public function check( $what = self::PARTS_ALL )
	{
		$this->checkParts( $what );

		$this->notifyListeners( 'check.before', $what );

		if( ( $what & self::PARTS_PRODUCT ) && ( count( $this->products ) < 1 ) ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Basket empty' ) );
		}

		$this->notifyListeners( 'check.after', $what );

		return $this;
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
	 * Checks the constants for the different parts of the basket.
	 *
	 * @param integer $value Part constant
	 * @throws \Aimeos\MShop\Order\Exception If parts constant is invalid
	 */
	protected function checkParts( $value )
	{
		$value = (int) $value;

		if( $value < self::PARTS_NONE || $value > self::PARTS_ALL ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Flags not within allowed range' ) );
		}
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

			// enforce the type and saving as new item
			$list[$item->getServiceId()] = $item->setType( $type )->setId( null );
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
