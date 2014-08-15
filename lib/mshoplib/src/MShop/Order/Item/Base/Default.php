<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Default implementation of the shopping basket.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Item_Base_Default extends MShop_Order_Item_Base_Abstract
{
	private $_price;
	private $_locale;
	private $_values;
	private $_products;
	private $_addresses;
	private $_services;
	private $_coupons;
	private $_modified = false;


	/**
	 * Initializes the shopping cart.
	 *
	 * @param MShop_Price_Item_Interface $price Default price of the basket (usually 0.00)
	 * @param MShop_Locale_Item_Interface $locale Locale item containing the site, language and currency
	 * @param array $values Associative list of key/value pairs containing
	 * 	e.g. the order or user ID
	 */
	public function __construct( MShop_Price_Item_Interface $price, MShop_Locale_Item_Interface $locale,
		array $values = array(), array $products = array(), array $addresses = array(),
		array $services = array(), array $coupons = array() )
	{
		MW_Common_Abstract::checkClassList( 'MShop_Order_Item_Base_Product_Interface', $products );
		MW_Common_Abstract::checkClassList( 'MShop_Order_Item_Base_Address_Interface', $addresses );
		MW_Common_Abstract::checkClassList( 'MShop_Order_Item_Base_Service_Interface', $services );

		foreach( $coupons as $couponProducts ) {
			MW_Common_Abstract::checkClassList( 'MShop_Order_Item_Base_Product_Interface', $couponProducts );
		}

		$this->_price = $price;
		$this->_locale = $locale;
		$this->_values = $values;
		$this->_products = $products;
		$this->_addresses = $addresses;
		$this->_services = $services;
		$this->_coupons = $coupons;

		$this->_modified = false;
	}


	/**
	 * Clones internal objects of the order base item.
	 */
	public function __clone()
	{
		$this->_price = clone $this->_price;
		$this->_locale = clone $this->_locale;

		foreach( $this->_products as $key => $value ) {
			$this->_products[$key] = $value;
		}

		foreach( $this->_addresses as $key => $value ) {
			$this->_addresses[$key] = $value;
		}

		foreach( $this->_services as $key => $value ) {
			$this->_services[$key] = $value;
		}

		foreach( $this->_coupons as $key => $value ) {
			$this->_coupons[$key] = $value;
		}
	}


	/**
	 * Returns the ID of the order if already available.
	 *
	 * @return string|null ID of the order item
	 */
	public function getId()
	{
		return ( isset( $this->_values['id'] ) ? (string) $this->_values['id'] : null );
	}


	/**
	 * Sets the id of the order base object.
	 *
	 * @param string $id Unique ID of the order base object
	 */
	public function setId( $id )
	{
		if ( ( $this->_values['id'] = MShop_Common_Item_Abstract::checkId($this->getId(), $id) ) === null ) {
			$this->_modified = true;
		} else {
			$this->_modified = false;
		}
	}


	/**
	 * Returns the ID of the site the item is stored.
	 *
	 * @return integer|null Site ID (or null if not available)
	 */
	public function getSiteId()
	{
		return ( isset( $this->_values['siteid'] ) ? (int) $this->_values['siteid'] : null );
	}


	/**
	 * Returns the code of the site the item is stored.
	 *
	 * @return string Site code (or empty string if not available)
	 */
	public function getSiteCode()
	{
		return ( isset( $this->_values['sitecode'] ) ? (string) $this->_values['sitecode'] : '' );
	}


	/**
	 * Returns the comment field of the order item.
	 *
	 * @return string Comment for the order
	 */
	public function getComment()
	{
		return ( isset( $this->_values['comment'] ) ? (string) $this->_values['comment'] : '' );
	}


	/**
	 * Sets the comment field of the order item
	 *
	 * @param string $comment Comment for the order
	 */
	public function setComment( $comment )
	{
		if ( $comment == $this->getComment() ) { return; }

		$this->_values['comment'] = (string) $comment;

		$this->_modified = true;
	}


	/**
	 * Returns the customer ID of the customer who has ordered.
	 *
	 * @return string Unique ID of the customer
	 */
	public function getCustomerId()
	{
		return ( isset( $this->_values['customerid'] ) ? (string) $this->_values['customerid'] : '' );
	}


	/**
	 * Sets the customer ID of the customer who has ordered.
	 *
	 * @param string $customerid Unique ID of the customer
	 */
	public function setCustomerId( $customerid )
	{
		if ( $customerid === $this->getCustomerId() ) { return; }

		$this->_notifyListeners( 'setCustomerId.before', $customerid );

		$this->_values['customerid'] = (string) $customerid;
		$this->_modified = true;

		$this->_notifyListeners( 'setCustomerId.after', $customerid );
	}


	/**
	 * Returns the locales for the basic order item.
	 *
	 * @return MShop_Locale_Item_Interface Object containing information
	 *  about site, language, country and currency
	 */
	public function getLocale()
	{
		return $this->_locale;
	}


	/**
	 * Sets the locales for the basic order item.
	 *
	 * @param MShop_Locale_Item_Interface $locale Object containing information
	 *  about site, language, country and currency
	 */
	public function setLocale( MShop_Locale_Item_Interface $locale )
	{
		$this->_notifyListeners( 'setLocale.before', $locale );

		$this->_locale = clone $locale;
		$this->_modified = true;

		$this->_notifyListeners( 'setLocale.after', $locale );
	}


	/**
	 * Returns the product items that are or should be part of an (future) order.
	 *
	 * @return array Array of order product items implementing MShop_Order_Item_Base_Product_Interface
	 */
	public function getProducts()
	{
		return $this->_products;
	}


	/**
	 * Returns the product item of an (future) order specified by its key.
	 *
	 * @param integer $key Key returned by getProducts() identifying the requested product
	 * @return MShop_Order_Item_Base_Product_Interface Product item of an order
	 */
	public function getProduct( $key )
	{
		if( !isset( $this->_products[$key] ) ) {
			throw new MShop_Order_Exception( sprintf( 'Product with array key "%1$d" not available', $key ) );
		}

		return $this->_products[$key];
	}


	/**
	 * Adds an order product item to the (future) order.
	 * If a similar item is found, only the quantity is increased.
	 *
	 * @param MShop_Order_Item_Base_Product_Interface $item Order product item to be added
	 * @param integer|null $position position of the new order product item
	 * @return integer Position the product item was inserted at
	 */
	public function addProduct( MShop_Order_Item_Base_Product_Interface $item, $position = null )
	{
		$this->_checkProduct( $item );
		$this->_checkPrice( $item->getPrice() );

		$this->_notifyListeners( 'addProduct.before', $item );

		if( ( $pos = $this->_getSameProduct( $item ) ) !== false )
		{
			$quantity = $item->getQuantity();
			$item = $this->_products[$pos];
			$item->setQuantity( $item->getQuantity() + $quantity );
		}
		else if( $position !== null )
		{
			if( isset( $this->_products[$position] ) )
			{
				$products = array();

				foreach( $this->_products as $key => $product )
				{
					if( $key < $position ) {
						$products[$key] = $product;
					} else if( $key >= $position ) {
						$products[$key+1] = $product;
					}
				}

				$products[$position] = $item;
				$this->_products = $products;
			}
			else
			{
				$this->_products[$position] = $item;
			}

			$pos = $position;
		}
		else
		{
			$this->_products[] = $item;
			end( $this->_products );
			$pos = key( $this->_products );
		}

		ksort( $this->_products );
		$this->_modified = true;

		$this->_notifyListeners( 'addProduct.after', $item );

		return $pos;
	}


	/**
	 * Deletes an order product item from the (future) order.
	 *
	 * @param integer $position Position id of the order product item
	 */
	public function deleteProduct( $position )
	{
		if( !array_key_exists( $position, $this->_products ) ) {
			throw new MShop_Order_Exception( sprintf( 'Product with array key "%1$d" not available', $position ) );
		}

		$this->_notifyListeners( 'deleteProduct.before', $position );

		unset($this->_products[$position]);
		$this->_modified = true;

		$this->_notifyListeners( 'deleteProduct.after', $position );
	}


	/**
	 * Returns all addresses that are part of the basket.
	 *
	 * @return array Associative list of address items implementing
	 *  MShop_Order_Item_Base_Address_Interface with "billing" or "delivery" as key
	 */
	public function getAddresses()
	{
		return $this->_addresses;
	}


	/**
	 * Returns the billing or delivery address depending on the given domain.
	 *
	 * @param string $domain Address domain, usually "billing" or "delivery"
	 * @return MShop_Order_Item_Base_Address_Interface Order address item for the requested domain
	 */
	public function getAddress( $domain = MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT )
	{
		if(!isset($this->_addresses[$domain])) {
			throw new MShop_Order_Exception( sprintf( 'Address for domain "%1$s" not available', $domain ) );
		}

		return $this->_addresses[$domain];
	}


	/**
	 * Sets a customer address as billing or delivery address for an order.
	 *
	 * @param MShop_Order_Item_Base_Address_Interface $address Order address item for the given domain
	 * @param string $domain Address domain, usually "billing" or "delivery"
	 * @return MShop_Order_Item_Base_Address_Interface Item that was really added to the basket
	 */
	public function setAddress( MShop_Order_Item_Base_Address_Interface $address,
		$domain = MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT )
	{
		if ( isset( $this->_addresses[ $domain ] ) && $this->_addresses[ $domain ] === $address ) { return; }

		$this->_notifyListeners( 'setAddress.before', $address );

		// enforce that the type is the same as the given one
		$address->setType( $domain );

		$this->_addresses[$domain] = clone $address;
		$this->_modified = true;

		$this->_notifyListeners( 'setAddress.after', $address );

		return $this->_addresses[$domain];
	}


	/**
	 * Deleted a customer address for billing or delivery of an order.
	 *
	 * @param string $domain Address domain defined in MShop_Order_Item_Base_Address_Abstract
	 */
	public function deleteAddress( $domain = MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY )
	{
		$this->_notifyListeners( 'deleteAddress.before', $domain );

		if( isset( $this->_addresses[$domain] ) ) {
			unset( $this->_addresses[$domain] );
			$this->_modified = true;
		}

		$this->_notifyListeners( 'deleteAddress.after', $domain );
	}


	/**
	 * Returns all services that are part of the basket.
	 *
	 * @return array Associative list of service items implementing MShop_Order_Service_Interface
	 *  with "delivery" or "payment" as key
	 */
	public function getServices()
	{
		return $this->_services;
	}


	/**
	 * Returns the delivery or payment service depending on the given type.
	 *
	 * @param string $type Service type code like 'payment', 'delivery', etc.
	 * @return MShop_Order_Item_Base_Serive_Interface Order service item for the requested type
	 */
	public function getService( $type )
	{
		if(!isset($this->_services[$type])) {
			throw new MShop_Order_Exception( sprintf( 'Service of type "%1$s" not available', $type ) );
		}

		return $this->_services[$type];
	}


	/**
	 * Sets a service as delivery or payment service for an order.
	 *
	 * @param MShop_Order_Item_Base_Service_Interface $service Order service item for the given domain
	 * @param string $type Service type
	 * @return MShop_Order_Item_Base_Service_Interface Item that was really added to the basket
	 */
	public function setService( MShop_Order_Item_Base_Service_Interface $service, $type )
	{
		$this->_checkPrice( $service->getPrice() );

		$this->_notifyListeners( 'setService.before', $service );

		// enforce that the type is the same as the given one
		$service->setType( $type );

		$this->_services[$type] = clone $service;
		$this->_modified = true;

		$this->_notifyListeners( 'setService.after', $service );

		return $this->_services[$type];
	}


	/**
	 * Deletes the delivery or payment service from the basket.
	 */
	public function deleteService( $type )
	{
		$this->_notifyListeners( 'deleteService.before', $type );

		if( isset( $this->_services[$type] ) ) {
			unset( $this->_services[$type] );
			$this->_modified = true;
		}

		$this->_notifyListeners( 'deleteService.after', $type );
	}


	/**
	 * Returns the available coupon codes and the lists of affected product items.
	 *
	 * @return array Associative array of codes and lists of product items
	 *  implementing MShop_Order_Product_Interface
	 */
	public function getCoupons()
	{
		return $this->_coupons;
	}


	/**
	 * Adds a coupon code entered by the customer and the given product item to the basket.
	 *
	 * @param string $code Coupon code
	 * @param MShop_Order_Item_Base_Product_Interface[] $products List of coupon products
	 */
	public function addCoupon( $code, array $products = array() )
	{
		if( isset( $this->_coupons[$code] ) ) {
			throw new MShop_Order_Exception( sprintf( 'Duplicate coupon code "%1$s"', $code ) );
		}

		foreach( $products as $product )
		{
			$this->_checkProduct( $product );
			$this->_checkPrice( $product->getPrice() );
		}

		$this->_notifyListeners( 'addCoupon.before', $products );

		$this->_coupons[$code] = $products;

		foreach( $products as $product ) {
			$this->_products[] = $product;
		}

		$this->_modified = true;

		$this->_notifyListeners( 'addCoupon.after', $code );
	}


	/**
	 * Removes a coupon and the related product items from the basket.
	 *
	 * @param string $code Coupon code
	 * @param boolean $removecode If the coupon code should also be removed
	 * @return array List of affected product items implementing MShop_Order_Item_Base_Product_Interface
	 *  or an empty list if no products are affected by a coupon
	 */
	public function deleteCoupon( $code, $removecode = false )
	{
		$products = array();

		if( isset( $this->_coupons[$code] ) )
		{
			$this->_notifyListeners( 'deleteCoupon.before', $code );

			$products = $this->_coupons[$code];

			foreach( $products as $product )
			{
				if( ( $key = array_search( $product, $this->_products, true ) ) !== false ) {
					unset( $this->_products[$key] );
				}
			}

			if( $removecode === true ) {
				unset( $this->_coupons[$code] );
			} else {
				$this->_coupons[$code] = array();
			}

			$this->_modified = true;

			$this->_notifyListeners( 'deleteCoupon.after' );
		}

		return $products;
	}


	/**
	 * Tests if all necessary items are available to create the order.
	 *
	 * @param integer $what Test for the specifice type of completeness
	 * @throws MShop_Order_Exception if there are no products in the basket
	 */
	public function check( $what = MShop_Order_Item_Base_Abstract::PARTS_ALL )
	{
		$this->_checkParts( $what );

		$this->_notifyListeners( 'check.before', $what );

		if( ( $what & MShop_Order_Item_Base_Abstract::PARTS_PRODUCT ) && ( count($this->_products) < 1 ) ) {
			throw new MShop_Order_Exception( sprintf( 'Basket empty' ) );
		}

		$this->_notifyListeners( 'check.after', $what );
	}


	/**
	 * Tests if the order object was modified.
	 *
	 * @return bool True if modified, false if not
	 */
	public function isModified()
	{
		return $this->_modified;
	}


	/**
	 * Returns a price item with amounts calculated for the products, costs, etc.
	 *
	 * @return MShop_Price_Item_Interface Price item with price, costs and rebate the customer has to pay
	 */
	public function getPrice()
	{
		if( $this->_modified !== false )
		{
			$this->_price->setValue( '0.00' );
			$this->_price->setCosts( '0.00' );
			$this->_price->setRebate( '0.00' );
			$this->_price->setTaxRate( '0.00' );

			foreach( $this->getServices() as $service ) {
				$this->_price->addItem( $service->getPrice() );
			}

			foreach( $this->getProducts() as $product ) {
				$this->_price->addItem( $product->getPrice(), $product->getQuantity() );
			}
		}

		return $this->_price;
	}


	/**
	 * Returns the current status of the order base item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the new status of the order base item.
	 *
	 * @param integer $value Status of the item
	 */
	public function setStatus( $value )
	{
		$this->_values['status'] = (int) $value;
		$this->_modified = true;
	}


	/**
	 * Returns modification time of the order item base product.
	 *
	 * @return string Returns modification time of the order base item
	 */
	public function getTimeModified()
	{
		return ( isset( $this->_values['mtime'] ) ? (string) $this->_values['mtime'] : null );
	}


	/**
	 * Returns the create date of the item.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeCreated()
	{
		return ( isset( $this->_values['ctime'] ) ? (string) $this->_values['ctime'] : null );
	}


	/**
	 * Returns the editor code of editor who created/modified the item at last.
	 *
	 * @return string Editorcode of editor who created/modified the item at last
	 */
	public function getEditor()
	{
		return ( isset( $this->_values['editor'] ) ? (string) $this->_values['editor'] : '' );
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$price = $this->_price;
		$locale = $this->_locale;

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
	 */
	public function finish()
	{
		$this->_notifyListeners( 'setOrder.before' );
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
		$this->_clearListeners();

		return array_keys( get_object_vars( $this ) );
	}


	/**
	 * Checks if the price uses the same currency as the price in the basket.
	 *
	 * @param MShop_Price_Item_Interface $item Price item
	 */
	protected function _checkPrice( MShop_Price_Item_Interface $item )
	{
		$price = clone $this->_price;
		$price->addItem( $item );
	}


	/**
	 * Tests if the given product is similar to an existing one.
	 * Similarity is described by the equality of properties so the quantity of
	 * the existing product can be updated.
	 *
	 * @param MShop_Order_Item_Base_Product_Interface $item Order product item
	 * @return integer Positon of the same product in the product list
	 * @throws MShop_Order_Exception If no similar item was found
	 */
	protected function _getSameProduct( MShop_Order_Item_Base_Product_Interface $item )
	{
		$attributeMap = array();

		foreach( $item->getAttributes() as $attributeItem ) {
			$attributeMap[ $attributeItem->getCode() ] = $attributeItem;
		}

		foreach( $this->_products as $position => $product )
		{
			if( $product->getProductCode() !== $item->getProductCode() ) {
				continue;
			}

			if( $product->getSupplierCode() !== $item->getSupplierCode() ) {
				continue;
			}

			if( $product->getFlags() !== $item->getFlags() ) {
				continue;
			}

			if( $product->getName() !== $item->getName() ) {
				continue;
			}

			if( $product->getPrice()->getValue() !== $item->getPrice()->getValue() ) {
				continue;
			}

			if( $product->getPrice()->getCosts() !== $item->getPrice()->getCosts() ) {
				continue;
			}

			if( $product->getPrice()->getRebate() !== $item->getPrice()->getRebate() ) {
				continue;
			}

			if( $product->getPrice()->getTaxRate() !== $item->getPrice()->getTaxRate() ) {
				continue;
			}

			$prodAttributes = $product->getAttributes();

			if( count( $prodAttributes ) !== count( $attributeMap ) ) {
				continue;
			}

			foreach( $prodAttributes as $attribute )
			{
				if( array_key_exists( $attribute->getCode(), $attributeMap ) === false
					|| $attributeMap[ $attribute->getCode() ]->getValue() != $attribute->getValue() ) {
					continue 2; // jump to outer loop
				}
			}

			return $position;
		}

		return false;
	}
}
