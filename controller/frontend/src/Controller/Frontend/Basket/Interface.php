<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Frontend
 */


/**
 * Interface for basket frontend controllers.
 *
 * @package Controller
 * @subpackage Frontend
 */
interface Controller_Frontend_Basket_Interface extends Controller_Frontend_Common_Interface
{
	/**
	 * Empties the basket and removing all products, addresses, services, etc.
	 * @return void
	 */
	public function clear();


	/**
	 * Returns the basket object.
	 *
	 * @return MShop_Order_Item_Base_Interface Basket holding products, addresses and delivery/payment options
	 */
	public function get();


	/**
	 * Adds a categorized product to the basket of the user stored in the session.
	 *
	 * @param string $prodid ID of the base product to add
	 * @param integer $quantity Amount of products that should by added
	 * @param array $options Possible options are: 'stock'=>true|false and 'variant'=>true|false
	 * 	The 'stock'=>false option allows adding products without being in stock.
	 * 	The 'variant'=>false option allows adding the selection product to the basket
	 * 	instead of the specific sub-product if the variant-building attribute IDs
	 * 	doesn't match a specific sub-product or if the attribute IDs are missing.
	 * @param array $variantAttributeIds List of variant-building attribute IDs that identify a specific product
	 * 	in a selection products
	 * @param array $configAttributeIds  List of attribute IDs that doesn't identify a specific product in a
	 * 	selection of products but are stored together with the product (e.g. for configurable products)
	 * @param array $hiddenAttributeIds List of attribute IDs that should be stored along with the product in the order
	 * @param array $customAttributeValues Associative list of attribute IDs and arbitrary values that should be stored
	 * 	along with the product in the order
	 * @param string $warehouse Unique code of the warehouse to deliver the products from
	 * @throws Controller_Frontend_Basket_Exception If the product isn't available
	 */
	public function addProduct( $prodid, $quantity = 1, array $options = array(), array $variantAttributeIds = array(),
		array $configAttributeIds = array(), array $hiddenAttributeIds = array(), array $customAttributeValues = array(),
		$warehouse = 'default' );


	/**
	 * Deletes a product item from the basket.
	 *
	 * @param integer $position Position number (key) of the order product item
	 * @return void
	 */
	public function deleteProduct( $position );


	/**
	 * Edits the quantity of a product item in the basket.
	 *
	 * @param integer $position Position number (key) of the order product item
	 * @param integer $quantity New quantiy of the product item
	 * @param array $configAttributeCodes Codes of the product config attributes that should be REMOVED
	 * @return void
	 */
	public function editProduct( $position, $quantity, array $configAttributeCodes = array() );


	/**
	 * Adds the given coupon code and updates the basket.
	 *
	 * @param string $code Coupon code entered by the user
	 * @throws Controller_Frontend_Basket_Exception if the coupon code is invalid or not allowed
	 * @return void
	 */
	public function addCoupon( $code );


	/**
	 * Removes the given coupon code and its effects from the basket.
	 *
	 * @param string $code Coupon code entered by the user
	 * @throws Controller_Frontend_Basket_Exception if the coupon code is invalid
	 * @return void
	 */
	public function deleteCoupon( $code );


	/**
	 * Sets the address of the customer in the basket.
	 *
	 * @param string $type Address type constant from MShop_Order_Item_Base_Address_Abstract
	 * @param MShop_Common_Item_Address_Interface|array|null $value Address object or array with key/value pairs of address or null to remove address from basket
	 * @throws Controller_Frontend_Basket_Exception If the billing or delivery address is not of any required type of
	 * 	if one of the keys is invalid when using an array with key/value pairs
	 * @return void
	 */
	public function setAddress( $type, $value );


	/**
	 * Sets the delivery/payment service item based on the service ID.
	 *
	 * @param string $type Service type code like 'payment' or 'delivery'
	 * @param string $id Unique ID of the service item
	 * @param array $attributes Associative list of key/value pairs containing the attributes selected or
	 * 	entered by the customer when choosing one of the delivery or payment options
	 * @throws Controller_Frontend_Basket_Exception If there is no price to the service item attached
	 * @return void
	 */
	public function setService( $type, $id, array $attributes = array() );
}
