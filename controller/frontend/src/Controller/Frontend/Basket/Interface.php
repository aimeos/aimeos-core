<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 * @version $Id: Interface.php 896 2012-07-04 12:25:26Z nsendetzky $
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
	 */
	public function clear();


	/**
	 * Returns the basket object.
	 *
	 * @return MShop_Order_Item_Base_Interface Basket holding products, addresses and delivery/payment options
	 */
	public function get();


	/**
	 * Adds a product to the basket of the user stored in the session.
	 *
	 * @param string $prodid ID of the base product to add
	 * @param integer $quantity Amount of products that should by added
	 * @param array $configAttributeIds  List of attribute IDs that doesn't identify a specific product in a
	 * 	selection of products but are stored together with the product (e.g. for configurable products)
	 * @param array $variantAttributeIds List of variant-building attribute IDs that identify a specific product
	 * 	in a selection products
	 * @param boolean $requireVariant True if a specific product must be matched by the variant-building attribute IDs
	 *  or false if the parent product can be added to the basket when the variant-building attributes don't match or
	 *  are missing
	 * @throws Controller_Frontend_Basket_Exception If the product isn't found
	 */
	public function addProduct( $prodid, $quantity = 1, $configAttributeIds = array(), $variantAttributeIds = array(), $requireVariant = true );


	/**
	 * Deletes a product item from the basket.
	 *
	 * @param integer $position Position number (key) of the order product item
	 */
	public function deleteProduct( $position );


	/**
	 * Edits the quantity of a product item in the basket.
	 *
	 * @param integer $position Position number (key) of the order product item
	 * @param integer $quantity New quantiy of the product item
	 * @param array $configAttributeCodes Codes of the product config attributes that should be REMOVED
	 */
	public function editProduct( $position, $quantity, $configAttributeCodes = array() );


	/**
	 * Sets the billing address of the customer in the basket.
	 *
	 * @param MShop_Common_Item_Address_Interface|array|string $billing Address object, array with key/value pairs or
	 *  ID of the customer. In case of an array, the keys must be the same as the keys returned when calling toArray()
	 *  on the billing address object like "customer.salutation"
	 * @throws Controller_Frontend_Basket_Exception If the billing or delivery address is not of any required type of
	 * 	if one of the keys is invalid when using an array with key/value pairs
	 */
	public function setBillingAddress( $billing );


	/**
	 * Sets the delivery address of the customer in the basket (only required if the delivery address is different
	 * 	from the billing address).
	 *
	 * @param MShop_Common_Item_Address_Interface|array|string|null $delivery Address object, array with key/value
	 * 	pairs or ID of the customer address. In case of an array, the keys must be the same as the keys returned when
	 * 	calling toArray() on the delivery address object like "customer.address.salutation".
	 * @throws Controller_Frontend_Basket_Exception If the billing or delivery address is not of any required type of
	 * 	if one of the keys is invalid when using an array with key/value pairs
	 */
	public function setDeliveryAddress( $delivery );


	/**
	 * Sets the delivery service item given by its ID to the basket.
	 *
	 * @param string $id Unique ID of the delivery service item
	 * @param array $attributes Associative list of key/value pairs containing the delivery attributes selected or
	 * 	entered by the customer when choosing one of the delivery options
	 * @throws Controller_Frontend_Basket_Exception If there is no price to the delivery service item attached
	 */
	public function setDeliveryOption( $id, array $attributes = array() );


	/**
	 * Sets the payment service item given by its ID to the basket.
	 *
	 * @param string $id Unique ID of the payment service item
	 * @param array $attributes Associative list of key/value pairs containing the payment attributes selected or
	 * 	entered by the customer when choosing one of the delivery options
	 * @throws Controller_Frontend_Basket_Exception If there is no price to the delivery service item attached
	 */
	public function setPaymentOption( $id, array $attributes = array() );
}
