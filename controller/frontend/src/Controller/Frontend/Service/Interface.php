<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 */


/**
 * Interface for service frontend controllers.
 *
 * @package Controller
 * @subpackage Frontend
 */
interface Controller_Frontend_Service_Interface
	extends Controller_Frontend_Common_Interface
{
	/**
	 * Returns the service items that are available for the service type and the content of the basket.
	 *
	 * @param string $type Service type, e.g. "delivery" (shipping related) or "payment" (payment related)
	 * @param MShop_Order_Item_Base_Interface $basket Basket of the user
	 * @param array $ref List of domains for which the items referenced by the services should be fetched too
	 * @return array List of service items implementing MShop_Service_Item_Interface with referenced items
	 */
	public function getServices( $type, MShop_Order_Item_Base_Interface $basket,
		$ref = array( 'media', 'price', 'text') );

	/**
	 * Returns the list of attribute definitions which must be used to render the input form where the customer can
	 * enter or chose the required data necessary by the service provider.
	 *
	 * @param string $type Service type, e.g. "delivery" (shipping related) or "payment" (payment related)
	 * @param string $serviceId Identifier of one of the service option returned by getService()
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return array List of attribute definitions implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getServiceAttributes( $type, $serviceId, MShop_Order_Item_Base_Interface $basket );

	/**
	 * Returns the price of the service.
	 *
	 * @param string $type Service type, e.g. "delivery" (shipping related) or "payment" (payment related)
	 * @param string $serviceId Identifier of one of the service option returned by getService()
	 * @param MShop_Order_Item_Base_Interface $basket Basket with products
	 * @return MShop_Price_Item_Interface Price item
	 * @throws Controller_Frontend_Service_Exception If no active service provider for this ID is available
	 * @throws MShop_Exception If service provider isn't available
	 * @throws Exception If an error occurs
	 */
	public function getServicePrice( $type, $serviceId, MShop_Order_Item_Base_Interface $basket );

	/**
	 * Returns a list of attributes that are invalid.
	 *
	 * @param string $type Service type, e.g. "delivery" (shipping related) or "payment" (payment related)
	 * @param string $serviceId Identifier of the service option chosen by the customer
	 * @param array $attributes List of key/value pairs with name of the attribute from attribute definition object as
	 * 	key and the string entered by the customer as value
	 * @return array List of key/value pairs of attributes keys and an error message for values that are invalid or
	 * 	missing
	 */
	public function checkServiceAttributes( $type, $serviceId, array $attributes );
}
