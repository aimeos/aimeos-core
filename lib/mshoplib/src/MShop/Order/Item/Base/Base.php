<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	 * Check no basket content.
	 * Don't check if the basket is ready for checkout or ordering.
	 */
	const PARTS_NONE = 0;

	/**
	 * Check basket for products.
	 * Checks if the basket complies to the product related requirements.
	 */
	const PARTS_PRODUCT = 1;

	/**
	 * Check basket for addresses.
	 * Checks if the basket complies to the address related requirements.
	 */
	const PARTS_ADDRESS = 2;

	/**
	 * Check basket for delivery/payment.
	 * Checks if the basket complies to the delivery/payment related
	 * requirements.
	 */
	const PARTS_SERVICE = 4;

	/**
	 * Check basket for all parts.
	 * This constant matches all other part constants.
	 */
	const PARTS_ALL = 7;


	protected $products;
	protected $addresses;
	protected $services;
	protected $coupons;
	private $modified = false;


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
		\Aimeos\MW\Common\Base::checkClassList( '\Aimeos\MShop\Order\Item\Base\Product\Iface', $products );
		\Aimeos\MW\Common\Base::checkClassList( '\Aimeos\MShop\Order\Item\Base\Address\Iface', $addresses );
		\Aimeos\MW\Common\Base::checkClassList( '\Aimeos\MShop\Order\Item\Base\Service\Iface', $services );

		foreach( $coupons as $couponProducts ) {
			\Aimeos\MW\Common\Base::checkClassList( '\Aimeos\MShop\Order\Item\Base\Product\Iface', $couponProducts );
		}

		$this->products = $products;
		$this->addresses = $addresses;
		$this->services = $services;
		$this->coupons = $coupons;
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
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'order/base';
	}


	/**
	 * Returns the product items that are or should be part of an (future) order.
	 *
	 * @return array Array of order product items implementing \Aimeos\MShop\Order\Item\Base\Product\Iface
	 */
	public function getProducts()
	{
		return $this->products;
	}


	/**
	 * Returns the product item of an (future) order specified by its key.
	 *
	 * @param integer $key Key returned by getProducts() identifying the requested product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Product item of an order
	 */
	public function getProduct( $key )
	{
		if( !isset( $this->products[$key] ) ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Product with array key "%1$d" not available', $key ) );
		}

		return $this->products[$key];
	}


	/**
	 * Adds an order product item to the (future) order.
	 * If a similar item is found, only the quantity is increased.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $item Order product item to be added
	 * @param integer|null $position position of the new order product item
	 * @return integer Position the product item was inserted at
	 */
	public function addProduct( \Aimeos\MShop\Order\Item\Base\Product\Iface $item, $position = null )
	{
		$this->checkProduct( $item );
		$this->checkPrice( $item->getPrice() );

		$this->notifyListeners( 'addProduct.before', $item );

		if( ( $pos = $this->getSameProduct( $item, $this->products ) ) !== false )
		{
			$quantity = $item->getQuantity();
			$item = $this->products[$pos];
			$item->setQuantity( $item->getQuantity() + $quantity );
		}
		else if( $position !== null )
		{
			if( isset( $this->products[$position] ) )
			{
				$products = [];

				foreach( $this->products as $key => $product )
				{
					if( $key < $position ) {
						$products[$key] = $product;
					} else if( $key >= $position ) {
						$products[$key + 1] = $product;
					}
				}

				$products[$position] = $item;
				$this->products = $products;
			}
			else
			{
				$this->products[$position] = $item;
			}

			$pos = $position;
		}
		else
		{
			$this->products[] = $item;
			end( $this->products );
			$pos = key( $this->products );
		}

		ksort( $this->products );
		$this->setModified();

		$this->notifyListeners( 'addProduct.after', $item );

		return $pos;
	}


	/**
	 * Deletes an order product item from the (future) order.
	 *
	 * @param integer $position Position id of the order product item
	 */
	public function deleteProduct( $position )
	{
		if( !array_key_exists( $position, $this->products ) ) {
			return;
		}

		$this->notifyListeners( 'deleteProduct.before', $position );

		$product = $this->products[$position];
		unset( $this->products[$position] );
		$this->setModified();

		$this->notifyListeners( 'deleteProduct.after', $product );
	}


	/**
	 * Returns all addresses that are part of the basket.
	 *
	 * @return array Associative list of address items implementing
	 *  \Aimeos\MShop\Order\Item\Base\Address\Iface with "billing" or "delivery" as key
	 */
	public function getAddresses()
	{
		return $this->addresses;
	}


	/**
	 * Returns the billing or delivery address depending on the given type.
	 *
	 * @param string $type Address type, usually "billing" or "delivery"
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order address item for the requested type
	 */
	public function getAddress( $type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT )
	{
		if( !isset( $this->addresses[$type] ) ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Address for type "%1$s" not available', $type ) );
		}

		return $this->addresses[$type];
	}


	/**
	 * Sets a customer address as billing or delivery address for an order.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Address\Iface $address Order address item for the given type
	 * @param string $type Address type, usually "billing" or "delivery"
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Item that was really added to the basket
	 */
	public function setAddress( \Aimeos\MShop\Order\Item\Base\Address\Iface $address, $type )
	{
		if( isset( $this->addresses[$type] ) && $this->addresses[$type] === $address ) { return $address; }

		$this->notifyListeners( 'setAddress.before', $address );

		$address = clone $address;
		$address->setType( $type ); // enforce that the type is the same as the given one
		$address->setId( null ); // enforce saving as new item

		$this->addresses[$type] = $address;
		$this->setModified();

		$this->notifyListeners( 'setAddress.after', $address );

		return $this->addresses[$type];
	}


	/**
	 * Deleted a customer address for billing or delivery of an order.
	 *
	 * @param string $type Address type defined in \Aimeos\MShop\Order\Item\Base\Address\Base
	 */
	public function deleteAddress( $type )
	{
		if( !isset( $this->addresses[$type] ) ) {
			return;
		}

		$this->notifyListeners( 'deleteAddress.before', $type );

		$address = $this->addresses[$type];
		unset( $this->addresses[$type] );
		$this->setModified();

		$this->notifyListeners( 'deleteAddress.after', $address );
	}


	/**
	 * Returns all services that are part of the basket.
	 *
	 * @return array Associative list of service items implementing \Aimeos\MShop\Order\Service\Iface
	 *  with "delivery" or "payment" as key
	 */
	public function getServices()
	{
		return $this->services;
	}


	/**
	 * Returns the delivery or payment service depending on the given type.
	 *
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
	 * @return \Aimeos\MShop\Order\Item\Base\Serive\Iface Order service item for the requested type
	 */
	public function getService( $type )
	{
		if( !isset( $this->services[$type] ) ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Service of type "%1$s" not available', $type ) );
		}

		return $this->services[$type];
	}


	/**
	 * Sets a service as delivery or payment service for an order.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Iface $service Order service item for the given domain
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Item that was really added to the basket
	 */
	public function setService( \Aimeos\MShop\Order\Item\Base\Service\Iface $service, $type )
	{
		$this->checkPrice( $service->getPrice() );

		$this->notifyListeners( 'setService.before', $service );

		$service = clone $service;
		$service->setType( $type ); // enforce that the type is the same as the given one
		$service->setId( null ); // enforce saving as new item

		$this->services[$type] = $service;
		$this->setModified();

		$this->notifyListeners( 'setService.after', $service );

		return $this->services[$type];
	}


	/**
	 * Deletes the delivery or payment service from the basket.
	 *
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
	 */
	public function deleteService( $type )
	{
		if( !isset( $this->services[$type] ) ) {
			return;
		}

		$this->notifyListeners( 'deleteService.before', $type );

		$service = $this->services[$type];
		unset( $this->services[$type] );
		$this->setModified();

		$this->notifyListeners( 'deleteService.after', $service );
	}


	/**
	 * Returns the available coupon codes and the lists of affected product items.
	 *
	 * @return array Associative array of codes and lists of product items
	 *  implementing \Aimeos\MShop\Order\Product\Iface
	 */
	public function getCoupons()
	{
		return $this->coupons;
	}


	/**
	 * Adds a coupon code entered by the customer and the given product item to the basket.
	 *
	 * @param string $code Coupon code
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $products List of coupon products
	 */
	public function addCoupon( $code, array $products = [] )
	{
		if( isset( $this->coupons[$code] ) ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Duplicate coupon code "%1$s"', $code ) );
		}

		foreach( $products as $product )
		{
			$this->checkProduct( $product );
			$this->checkPrice( $product->getPrice() );
		}

		$this->notifyListeners( 'addCoupon.before', $products );

		$this->coupons[$code] = $products;

		foreach( $products as $product ) {
			$this->products[] = $product;
		}

		$this->setModified();

		$this->notifyListeners( 'addCoupon.after', $code );
	}


	/**
	 * Removes a coupon and the related product items from the basket.
	 *
	 * @param string $code Coupon code
	 * @param boolean $removecode If the coupon code should also be removed
	 * @return array List of affected product items implementing \Aimeos\MShop\Order\Item\Base\Product\Iface
	 *  or an empty list if no products are affected by a coupon
	 */
	public function deleteCoupon( $code, $removecode = false )
	{
		$products = [];

		if( isset( $this->coupons[$code] ) )
		{
			$this->notifyListeners( 'deleteCoupon.before', $code );

			$products = $this->coupons[$code];

			foreach( $products as $product )
			{
				if( ( $key = array_search( $product, $this->products, true ) ) !== false ) {
					unset( $this->products[$key] );
				}
			}

			if( $removecode === true ) {
				unset( $this->coupons[$code] );
			} else {
				$this->coupons[$code] = [];
			}

			$this->setModified();

			$this->notifyListeners( 'deleteCoupon.after', $code );
		}

		return $products;
	}


	/**
	 * Tests if all necessary items are available to create the order.
	 *
	 * @param integer $what Test for the specific type of completeness
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
	 */
	public function setModified()
	{
		$this->modified = true;
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
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Flags "%1$s" not within allowed range', $value ) );
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
	 * Checks if a order product contains all required values.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $item Order product item
	 * @throws \Aimeos\MShop\Exception if the price item or product code is missing
	 */
	protected function checkProduct( \Aimeos\MShop\Order\Item\Base\Product\Iface $item )
	{
		if( $item->getProductCode() === '' ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Product does not contain all required values. Product code for item not available.' ) );
		}
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

		foreach( $item->getAttributes() as $attributeItem ) {
			$attributeMap[$attributeItem->getCode()] = $attributeItem;
		}

		foreach( $products as $position => $product )
		{
			if( $product->compare( $item ) === false ) {
				continue;
			}

			$prodAttributes = $product->getAttributes();

			if( count( $prodAttributes ) !== count( $attributeMap ) ) {
				continue;
			}

			foreach( $prodAttributes as $attribute )
			{
				if( array_key_exists( $attribute->getCode(), $attributeMap ) === false
					|| $attributeMap[$attribute->getCode()]->getValue() != $attribute->getValue() ) {
					continue 2; // jump to outer loop
				}
			}

			return $position;
		}

		return false;
	}
}
