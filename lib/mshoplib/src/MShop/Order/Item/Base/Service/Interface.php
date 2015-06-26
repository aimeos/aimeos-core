<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Interface for order item base service.
 *
 * @package MShop
 * @subpackage Order
 */
interface MShop_Order_Item_Base_Service_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the order base ID of the order service if available.
	 *
	 * @return integer|null Base ID of the item.
	 */
	public function getBaseId();

	/**
	 * Sets the order service base ID of the order service item.
	 *
	 * @param integer|null Order service base ID
	 */
	public function setBaseId( $id );

	/**
	 * Returns the original ID of the service item used for the order.
	 *
	 * @return string Original service ID
	 */
	public function getServiceId();

	/**
	 * Sets a new ID of the service item used for the order.
	 *
	 * @param string $servid ID of the service item used for the order
	 */
	public function setServiceId( $servid );

	/**
	 * Returns the code of the service item.
	 *
	 * @return string Service item code
	 */
	public function getCode();

	/**
	 * Sets a new code for the service item.
	 *
	 * @param string $code Code as defined by the service provider
	 */
	public function setCode($code);

	/**
	 * Returns the name of the service item.
	 *
	 * @return string service item name
	 */
	public function getName();

	/**
	 * Sets a new name for the service item.
	 *
	 * @param string $name Service item name
	 */
	public function setName($name);

	/**
	 * Returns the type of the service item.
	 *
	 * @return string Service item type
	 */
	public function getType();

	/**
	 * Sets a new type for the service item.
	 *
	 * @param string $type type of the service item.
	 */
	public function setType($type);

	/**
	 * Returns the price object which belongs to the service item.
	 *
	 * @return MShop_Price_Item_Interface Price item
	 */
	public function getPrice();

	/**
	 * Sets a new price object for the service item.
	 *
	 * @param MShop_Price_Item_Interface $price Price item
	 */
	public function setPrice(MShop_Price_Item_Interface $price);

	/**
	 * Returns the value of the attribute item for the service with the given code.
	 *
	 * @param string $code Code of the service attribute item
	 * @param string $type Type of the service attribute item
	 * @return string|null value of the attribute item for the service and the given code
	 */
	public function getAttribute( $code, $type = 'default' );

	/**
	 * Returns the attribute item for the service with the given code.
	 *
	 * @param string $code Code of the service attribute item
	 * @param string $type Type of the service attribute item
	 * @return MShop_Order_Item_Base_Service_Attribute_Interface|null Attribute item for the service and the given code
	 */
	public function getAttributeItem( $code, $type = 'default' );

	/**
	 * Adds or replaces the attribute item in the list of service attributes.
	 *
	 * @param MShop_Order_Item_Base_Service_Attribute_Interface $item Service attribute item
	 */
	public function setAttributeItem( MShop_Order_Item_Base_Service_Attribute_Interface $item );

	/**
	 * Returns the list of attribute items for the service.
	 *
	 * @param string|null $type Filters returned attributes by the given type or null for no filtering
	 * @return array List of attribute items implementing MShop_Order_Item_Base_Service_Attribute_Interface
	 */
	public function getAttributes( $type = null );

	/**
	 * Sets the new list of attribute items for the service.
	 *
	 * @param array $attributes List of attribute items implementing MShop_Order_Item_Base_Service_Attribute_Interface
	 */
	public function setAttributes( array $attributes );

	/**
	 * Copys all data from a given service item.
	 *
	 * @param MShop_Service_Item_Interface $service New service item
	 */
	public function copyFrom( MShop_Service_Item_Interface $service );

	/**
	 * Sets the media url of the service item.
	 *
	 * @param string $value Location of the media/picture
	 */
	public function setMediaUrl( $value );

	/**
	 * Returns the location of the media.
	 *
	 * @return string Location of the media
	 */
	public function getMediaUrl();

}
