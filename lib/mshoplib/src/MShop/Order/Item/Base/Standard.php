<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base;


/**
 * Default implementation of the shopping basket.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard extends \Aimeos\MShop\Order\Item\Base\Base
{
	private $price;
	private $locale;
	private $values;
	private $products;
	private $addresses;
	private $services;
	private $coupons;
	private $modified = false;


	/**
	 * Initializes the shopping cart.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Default price of the basket (usually 0.00)
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Locale item containing the site, language and currency
	 * @param array $values Associative list of key/value pairs containing
	 * 	e.g. the order or user ID
	 */
	public function __construct( \Aimeos\MShop\Price\Item\Iface $price, \Aimeos\MShop\Locale\Item\Iface $locale,
		array $values = array(), array $products = array(), array $addresses = array(),
		array $services = array(), array $coupons = array() )
	{
		\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MShop\\Order\\Item\\Base\\Product\\Iface', $products );
		\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MShop\\Order\\Item\\Base\\Address\\Iface', $addresses );
		\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MShop\\Order\\Item\\Base\\Service\\Iface', $services );

		foreach( $coupons as $couponProducts ) {
			\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MShop\\Order\\Item\\Base\\Product\\Iface', $couponProducts );
		}

		$this->price = $price;
		$this->locale = $locale;
		$this->values = $values;
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
		$this->price = clone $this->price;
		$this->locale = clone $this->locale;

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
	 * Returns the ID of the order if already available.
	 *
	 * @return string|null ID of the order item
	 */
	public function getId()
	{
		if( isset( $this->values['order.base.id'] ) ) {
			return (string) $this->values['order.base.id'];
		}

		return null;
	}


	/**
	 * Sets the id of the order base object.
	 *
	 * @param string $id Unique ID of the order base object
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function setId( $id )
	{
		if( ( $this->values['order.base.id'] = \Aimeos\MShop\Common\Item\Base::checkId( $this->getId(), $id ) ) === null ) {
			$this->modified = true;
		} else {
			$this->modified = false;
		}

		return $this;
	}


	/**
	 * Returns the ID of the site the item is stored.
	 *
	 * @return integer|null Site ID (or null if not available)
	 */
	public function getSiteId()
	{
		if( isset( $this->values['order.base.siteid'] ) ) {
			return (int) $this->values['order.base.siteid'];
		}

		return null;
	}


	/**
	 * Returns the code of the site the item is stored.
	 *
	 * @return string Site code (or empty string if not available)
	 */
	public function getSiteCode()
	{
		if( isset( $this->values['order.base.sitecode'] ) ) {
			return (string) $this->values['order.base.sitecode'];
		}

		return '';
	}


	/**
	 * Returns the comment field of the order item.
	 *
	 * @return string Comment for the order
	 */
	public function getComment()
	{
		if( isset( $this->values['order.base.comment'] ) ) {
			return (string) $this->values['order.base.comment'];
		}

		return '';
	}


	/**
	 * Sets the comment field of the order item
	 *
	 * @param string $comment Comment for the order
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function setComment( $comment )
	{
		if( $comment == $this->getComment() ) { return; }

		$this->values['order.base.comment'] = (string) $comment;
		$this->modified = true;

		return $this;
	}


	/**
	 * Returns the current status of the order base item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		if( isset( $this->values['order.base.status'] ) ) {
			return (int) $this->values['order.base.status'];
		}

		return 0;
	}


	/**
	 * Sets the new status of the order base item.
	 *
	 * @param integer $value Status of the item
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function setStatus( $value )
	{
		$this->values['order.base.status'] = (int) $value;
		$this->modified = true;

		return $this;
	}


	/**
	 * Returns modification time of the order item base product.
	 *
	 * @return string|null Returns modification time of the order base item
	 */
	public function getTimeModified()
	{
		if( isset( $this->values['order.base.mtime'] ) ) {
			return (string) $this->values['order.base.mtime'];
		}

		return null;
	}


	/**
	 * Returns the create date of the item.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeCreated()
	{
		if( isset( $this->values['order.base.ctime'] ) ) {
			return (string) $this->values['order.base.ctime'];
		}

		return null;
	}


	/**
	 * Returns the editor code of editor who created/modified the item at last.
	 *
	 * @return string Editorcode of editor who created/modified the item at last
	 */
	public function getEditor()
	{
		if( isset( $this->values['order.base.editor'] ) ) {
			return (string) $this->values['order.base.editor'];
		}

		return '';
	}


	/**
	 * Returns the customer ID of the customer who has ordered.
	 *
	 * @return string Unique ID of the customer
	 */
	public function getCustomerId()
	{
		if( isset( $this->values['order.base.customerid'] ) ) {
			return (string) $this->values['order.base.customerid'];
		}

		return '';
	}


	/**
	 * Sets the customer ID of the customer who has ordered.
	 *
	 * @param string $customerid Unique ID of the customer
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function setCustomerId( $customerid )
	{
		if( $customerid === $this->getCustomerId() ) { return; }

		$this->notifyListeners( 'setCustomerId.before', $customerid );

		$this->values['order.base.customerid'] = (string) $customerid;
		$this->setModified();

		$this->notifyListeners( 'setCustomerId.after', $customerid );

		return $this;
	}


	/**
	 * Returns the locales for the basic order item.
	 *
	 * @return \Aimeos\MShop\Locale\Item\Iface Object containing information
	 *  about site, language, country and currency
	 */
	public function getLocale()
	{
		return $this->locale;
	}


	/**
	 * Sets the locales for the basic order item.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Object containing information
	 *  about site, language, country and currency
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function setLocale( \Aimeos\MShop\Locale\Item\Iface $locale )
	{
		$this->notifyListeners( 'setLocale.before', $locale );

		$this->locale = clone $locale;
		$this->setModified();

		$this->notifyListeners( 'setLocale.after', $locale );

		return $this;
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

		if( ( $pos = $this->getSameProduct( $item ) ) !== false )
		{
			$quantity = $item->getQuantity();
			$item = $this->products[$pos];
			$item->setQuantity( $item->getQuantity() + $quantity );
		}
		else if( $position !== null )
		{
			if( isset( $this->products[$position] ) )
			{
				$products = array();

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
	 * Returns the billing or delivery address depending on the given domain.
	 *
	 * @param string $domain Address domain, usually "billing" or "delivery"
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order address item for the requested domain
	 */
	public function getAddress( $domain = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT )
	{
		if( !isset( $this->addresses[$domain] ) ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Address for domain "%1$s" not available', $domain ) );
		}

		return $this->addresses[$domain];
	}


	/**
	 * Sets a customer address as billing or delivery address for an order.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Address\Iface $address Order address item for the given domain
	 * @param string $domain Address domain, usually "billing" or "delivery"
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Item that was really added to the basket
	 */
	public function setAddress( \Aimeos\MShop\Order\Item\Base\Address\Iface $address,
		$domain = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT )
	{
		if( isset( $this->addresses[$domain] ) && $this->addresses[$domain] === $address ) { return; }

		$this->notifyListeners( 'setAddress.before', $address );

		$address = clone $address;
		$address->setType( $domain ); // enforce that the type is the same as the given one
		$address->setId( null ); // enforce saving as new item

		$this->addresses[$domain] = $address;
		$this->setModified();

		$this->notifyListeners( 'setAddress.after', $address );

		return $this->addresses[$domain];
	}


	/**
	 * Deleted a customer address for billing or delivery of an order.
	 *
	 * @param string $type Address type defined in \Aimeos\MShop\Order\Item\Base\Address\Base
	 */
	public function deleteAddress( $type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY )
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
	 * @param string $type Service type code like 'payment', 'delivery', etc.
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
	 * @param string $type Service type
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
	public function addCoupon( $code, array $products = array() )
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
		$products = array();

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
				$this->coupons[$code] = array();
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
	public function check( $what = \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL )
	{
		$this->checkParts( $what );

		$this->notifyListeners( 'check.before', $what );

		if( ( $what & \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT ) && ( count( $this->products ) < 1 ) ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Basket empty' ) );
		}

		$this->notifyListeners( 'check.after', $what );
	}


	/**
	 * Returns a price item with amounts calculated for the products, costs, etc.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface Price item with price, costs and rebate the customer has to pay
	 */
	public function getPrice()
	{
		if( $this->price->getValue() === '0.00' )
		{
			$this->price->clear();

			foreach( $this->getServices() as $service ) {
				$this->price->addItem( $service->getPrice() );
			}

			foreach( $this->getProducts() as $product ) {
				$this->price->addItem( $product->getPrice(), $product->getQuantity() );
			}
		}

		return $this->price;
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
		$this->price->clear();
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = array();

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.base.id': $this->setId( $value ); break;
				case 'order.base.comment': $this->setComment( $value ); break;
				case 'order.base.customerid': $this->setCustomerId( $value ); break;
				case 'order.base.status': $this->setStatus( $value ); break;
				case 'order.base.languageid': $this->locale->setLanguageId( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		unset( $list['order.base.siteid'] );
		unset( $list['order.base.ctime'] );
		unset( $list['order.base.mtime'] );
		unset( $list['order.base.editor'] );

		return $unknown;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$price = $this->price;
		$locale = $this->locale;

		return array(
			'order.base.id' => $this->getId(),
			'order.base.siteid' => $this->getSiteId(),
			'order.base.sitecode' => $this->getSiteCode(),
			'order.base.languageid' => $locale->getLanguageId(),
			'order.base.comment' => $this->getComment(),
			'order.base.customerid' => $this->getCustomerId(),
			'order.base.price' => $price->getValue(),
			'order.base.costs' => $price->getCosts(),
			'order.base.rebate' => $price->getRebate(),
			'order.base.currencyid' => $price->getCurrencyId(),
			'order.base.status' => $this->getStatus(),
			'order.base.mtime' => $this->getTimeModified(),
			'order.base.ctime' => $this->getTimeCreated(),
			'order.base.editor' => $this->getEditor(),
		);
	}


	/**
	 * Notifies listeners before the basket becomes an order.
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function finish()
	{
		$this->notifyListeners( 'setOrder.before' );
		return $this;
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
		 * Listeners will be reattached on wakeup by the customer manager
		 */
		$this->clearListeners();

		return array_keys( get_object_vars( $this ) );
	}


	/**
	 * Checks if the price uses the same currency as the price in the basket.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $item Price item
	 */
	protected function checkPrice( \Aimeos\MShop\Price\Item\Iface $item )
	{
		$price = clone $this->price;
		$price->addItem( $item );
	}


	/**
	 * Tests if the given product is similar to an existing one.
	 * Similarity is described by the equality of properties so the quantity of
	 * the existing product can be updated.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $item Order product item
	 * @return integer Positon of the same product in the product list
	 * @throws \Aimeos\MShop\Order\Exception If no similar item was found
	 */
	protected function getSameProduct( \Aimeos\MShop\Order\Item\Base\Product\Iface $item )
	{
		$attributeMap = array();

		foreach( $item->getAttributes() as $attributeItem ) {
			$attributeMap[$attributeItem->getCode()] = $attributeItem;
		}

		foreach( $this->products as $position => $product )
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
