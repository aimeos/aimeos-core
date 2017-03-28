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
 * Generic interface for order base items (shopping basket).
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface extends \Aimeos\MW\Observer\Publisher\Iface, \Aimeos\MShop\Common\Item\Iface
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
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function setComment( $comment );

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
	 * @param string $customerid Unique ID of the customer
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function setCustomerId( $customerid );

	/**
	 * Returns the locales for the basic order item.
	 *
	 * @return \Aimeos\MShop\Locale\Item\Iface Object containing information about site, language, country and currency
	 */
	public function getLocale();

	/**
	 * Sets the locales for the basic order item.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Object containing information about site, language, country and currency
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function setLocale( \Aimeos\MShop\Locale\Item\Iface $locale );

	/**
	 * Returns the product items that are or should be part of an (future) order.
	 *
	 * @return array Array of order product items implementing \Aimeos\MShop\Order\Product\Iface
	 */
	public function getProducts();

	/**
	 * Returns the product item of an (future) order specified by its key.
	 *
	 * @param integer $key Key returned by getProducts() identifying the requested product
	 * @return \Aimeos\MShop\Order\Product\Iface Product item of an order
	 */
	public function getProduct( $key );

	/**
	 * Adds an order product item to the (future) order.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $item Order product item to be added
	 * @param integer|null $position position of the new order product item
	 * @return integer Position the product item was inserted at
	 */
	public function addProduct( \Aimeos\MShop\Order\Item\Base\Product\Iface $item, $position = null );

	/**
	 * Deletes an order product item from the (future) order.
	 *
	 * @param integer $position Position id of the order product item
	 * @return null
	 */
	public function deleteProduct( $position );

	/**
	 * Returns all set addresses of the (future) order.
	 *
	 * @return array Array of \Aimeos\MShop\Order\Item\Base\Address\Iface order address items
	 */
	public function getAddresses();

	/**
	 * Returns the billing or delivery address depending on the given type.
	 *
	 * @param string $type Address type defined in \Aimeos\MShop\Order\Item\Base\Address\Base
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order address item for the requested type
	 */
	public function getAddress( $type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );

	/**
	 * Sets a customer address as billing or delivery address for an order.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Address\Iface $address Order address item for the given type
	 * @param string $type Address type defined in \Aimeos\MShop\Order\Item\Base\Address\Base
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Item that was really added to the basket
	 */
	public function setAddress( \Aimeos\MShop\Order\Item\Base\Address\Iface $address, $type );

	/**
	 * Deleted a customer address for billing or delivery of an order.
	 *
	 * @param string $type Address type defined in \Aimeos\MShop\Order\Item\Base\Address\Base
	 * @return null
	 */
	public function deleteAddress( $type );

	/**
	 * Returns all services (delivery, payment, etc.) attached to the shopping basket.
	 *
	 * @return array Array of \Aimeos\MShop\Order\Item\Base\Service\Iface Order service items
	 */
	public function getServices();

	/**
	 * Returns the payment or delivery service depending on the given type.
	 *
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order service item for the requested type
	 */
	public function getService( $type );

	/**
	 * Sets a service as payment or delivery service for an order.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Iface $service Order service item for the given type
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Item that was really added to the basket
	 */
	public function setService( \Aimeos\MShop\Order\Item\Base\Service\Iface $service, $type );

	/**
	 * Deletes the delivery or payment service from the basket.
	 *
	 * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
	 * @return null
	 */
	public function deleteService( $type );

	/**
	 * Returns the available coupon codes and the lists of affected product items.
	 *
	 * @return array Associative array of codes and lists of product items implementing
	 * \Aimeos\MShop\Order\Item\Base\Product\Iface
	 */
	public function getCoupons();

	/**
	 * Adds a coupon code entered by the customer and the given product item to the basket.
	 *
	 * @param string $code Coupon code
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $products List of coupon products
	 * @return null
	 */
	public function addCoupon( $code, array $products = [] );

	/**
	 * Removes a coupon from the order.
	 *
	 * @param string $code Coupon code
	 * @param boolean $removecode If the coupon code should also be removed
	 * @return array List of affected product items implementing \Aimeos\MShop\Order\Item\Base\Product\Iface
	 *  or an empty list if no products are affected by a coupon
	 */
	public function deleteCoupon( $code, $removecode = false );

	/**
	 * Returns a price item with amounts calculated for the products, shipping costs and rebate.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface Price Item containing price, shipping, rebate, etc.
	 */
	public function getPrice();

	/**
	 * Tests if all necessary items are available to create the order.
	 *
	 * @param integer $what Test for the specifice type of completeness
	 * @return bool
	 */
	public function check( $what = \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL );

	/**
	 * Notifies listeners before the basket becomes an order.
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
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
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function setStatus( $value );

	/**
	 * Tests if the order object was modified.
	 *
	 * @return bool True if modified, false if not
	 */
	public function isModified();

	/**
	 * Sets the modified flag of the object.
	 *
	 * @return null
	 */
	public function setModified();
}
