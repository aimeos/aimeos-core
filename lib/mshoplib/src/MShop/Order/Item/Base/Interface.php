<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Generic interface for order base items (shopping basket).
 *
 * @package MShop
 * @subpackage Order
 */
interface MShop_Order_Item_Base_Interface extends MW_Observer_Publisher_Interface, MShop_Common_Item_Interface
{
	/**
	 * Returns the comment field of the order item
	 *
	 * @return string Comment for the order
	 */
	public function getComment();

	/**
	 * Sets the comment field of the order item
	 *
	 * @param string $comment Comment for the order
	 */
	public function setComment($comment);

	/**
	 * Returns the code of the site the order was stored in.
	 *
	 * @return string Site code (or empty string if not available)
	 */
	public function getSiteCode();

	/**
	 * Returns the customer code of the customer who has ordered.
	 *
	 * @return string Unique ID of the customer
	 */
	public function getCustomerId();

	/**
	 * Sets the customer code of the customer who has ordered.
	 *
	 * @param string $customerID Unique ID of the customer
	 */
	public function setCustomerId($customerid);

	/**
	 * Returns the locales for the basic order item.
	 *
	 * @return MShop_Locale_Item_Interface Object containing information about site, language, country and currency
	 */
	public function getLocale();

	/**
	 * Sets the locales for the basic order item.
	 *
	 * @param MShop_Locale_Item_Interface $locale Object containing information about site, language, country and currency
	 */
	public function setLocale( MShop_Locale_Item_Interface $locale );

	/**
	 * Returns the product items that are or should be part of an (future) order.
	 *
	 * @return array Array of order product items implementing MShop_Order_Product_Interface
	 */
	public function getProducts();

	/**
	 * Returns the product item of an (future) order specified by its key.
	 *
	 * @param mixed $key Key returned by getProducts() identifying the requested product
	 * @return MShop_Order_Product_Interface Product item of an order
	 */
	public function getProduct( $key );

	/**
	 * Adds an order product item to the (future) order.
	 *
	 * @param MShop_Order_Item_Base_Product_Interface $item Order product item to be added
	 * @param integer|null $position position of the new order product item
	 * @return integer Position the product item was inserted at
	 */
	public function addProduct( MShop_Order_Item_Base_Product_Interface $item, $position=null );

	/**
	 * Deletes an order product item from the (future) order.
	 *
	 * @param integer $position Position id of the order product item
	 */
	public function deleteProduct( $position );

	/**
	 * Returns all set addresses of the (future) order.
	 *
	 * @return array Array of MShop_Order_Item_Base_Address_Interface order address items
	 */
	public function getAddresses();

	/**
	 * Returns the billing or delivery address depending on the given domain.
	 *
	 * @param string $domain Address domain defined in MShop_Order_Item_Base_Address_Abstract
	 * @return MShop_Order_Item_Base_Address_Interface Order address item for the requested domain
	 */
	public function getAddress( $domain = MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );

	/**
	 * Sets a customer address as billing or delivery address for an order.
	 *
	 * @param MShop_Order_Item_Base_Address_Interface $address Order address item for the given domain
	 * @param string $domain Address domain defined in MShop_Order_Item_Base_Address_Abstract
	 * @return MShop_Order_Item_Base_Address_Interface Item that was really added to the basket
	 */
	public function setAddress( MShop_Order_Item_Base_Address_Interface $address,
		$domain = MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY
	);

	/**
	 * Deleted a customer address for billing or delivery of an order.
	 *
	 * @param string $domain Address domain defined in MShop_Order_Item_Base_Address_Abstract
	 */
	public function deleteAddress( $domain = MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );

	/**
	 * Returns all services (delivery, payment, etc.) attached to the shopping basket.
	 *
	 * @return array Array of MShop_Order_Item_Base_Service_Interface Order service items
	 */
	public function getServices();

	/**
	 * Returns the payment or delivery service depending on the given domain.
	 *
	 * @param string $type Service domain
	 * @return MShop_Order_Item_Base_Service_Interface Order service item for the requested domain
	 */
	public function getService( $type );

	/**
	 * Sets a service as payment or delivery service for an order.
	 *
	 * @param MShop_Order_Item_Base_Service_Interface $service Order service item for the given domain
	 * @param string $type Service type
	 * @return MShop_Order_Item_Base_Service_Interface Item that was really added to the basket
	 */
	public function setService( MShop_Order_Item_Base_Service_Interface $service, $type );

	/**
	 * Returns the available coupon codes and the lists of affected product items.
	 *
	 * @return array Associative array of codes and lists of product items implementing
	 * MShop_Order_Item_Base_Product_Interface
	 */
	public function getCoupons();

	/**
	 * Adds a coupon code entered by the customer and the given product item to the basket.
	 *
	 * @param string $code Coupon code
	 * @param array $products List of coupon products
	 */
	public function addCoupon( $code, array $products = array() );

	/**
	 * Removes a coupon from the order.
	 *
	 * @param string $code Coupon code
	 * @param boolean $removecode If the coupon code should also be removed
	 * @return array List of affected product items implementing MShop_Order_Item_Base_Product_Interface
	 *  or an empty list if no products are affected by a coupon
	 */
	public function deleteCoupon( $code, $removecode = false );

	/**
	 * Returns a price item with amounts calculated for the products, shipping costs and rebate.
	 *
	 * @return MShop_Price_Item_Interface Price Item containing price, shipping, rebate, etc.
	 */
	public function getPrice();

	/**
	 * Tests if all necessary items are available to create the order.
	 *
	 * @param integer $what Test for the specifice type of completeness
	 * @return bool
	 */
	public function check( $what = MShop_Order_Item_Base_Abstract::PARTS_ALL );

	/**
	 * Notifies listeners before the basket becomes an order.
	 */
	public function finish();

	/**
	 * Returns the current status of the order base item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus();


	/**
	 * Sets the new status of the order base item.
	 *
	 * @param integer $value Status of the item
	 */
	public function setStatus( $value );

}
