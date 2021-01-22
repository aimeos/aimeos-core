<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item;


/**
 * Generic interface for all items.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Returns the item property for the given name
	 *
	 * @param string $name Name of the property
	 * @return mixed Property value or null if property is unknown
	 */
	public function __get( string $name );

	/**
	 * Tests if the item property for the given name is available
	 *
	 * @param string $name Name of the property
	 * @return bool True if the property exists, false if not
	 */
	public function __isset( string $name ) : bool;


	/**
	 * Sets the new item property for the given name
	 *
	 * @param string $name Name of the property
	 * @param mixed $value New property value
	 */
	public function __set( string $name, $value );

	/**
	 * Returns the ID of the items
	 *
	 * @return string ID of the item or null
	 */
	public function __toString() : string;

	/**
	 * Assigns multiple key/value pairs to the item
	 *
	 * @param iterable $pairs Associative list of key/value pairs
	 * @return \Aimeos\MShop\Common\Item\Iface Item for method chaining
	 */
	public function assign( iterable $pairs ) : \Aimeos\MShop\Common\Item\Iface;

	/**
	 * Returns the item property for the given name
	 *
	 * @param string $name Name of the property
	 * @param mixed $default Default value if property is unknown
	 * @return mixed|null Property value or default value if property is unknown
	 */
	public function get( string $name, $default = null );

	/**
	 * Sets the new item property for the given name
	 *
	 * @param string $name Name of the property
	 * @param mixed $value New property value
	 * @return \Aimeos\MShop\Common\Item\Iface Item for method chaining
	 */
	public function set( string $name, $value ) : \Aimeos\MShop\Common\Item\Iface;

	/**
	 * Returns the unique ID of the item.
	 *
	 * @return string|null ID of the item
	 */
	public function getId() : ?string;

	/**
	 * Sets the unique ID of the item.
	 *
	 * @param string|null $id Unique ID of the item
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setId( ?string $id ) : \Aimeos\MShop\Common\Item\Iface;

	/**
	 * Returns the ID of the site the item is stored
	 *
	 * @return string Site ID (or null if not available)
	 */
	public function getSiteId() : string;

	/**
	 * Returns the create date of the item.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeCreated() : ?string;

	/**
	 * Returns the time of last modification.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeModified() : ?string;

	/**
	 * Returns the user code of user who created/modified the item at last.
	 *
	 * @return string|null User code of user who created/modified the item at last
	 */
	public function getEditor() : string;

	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string;

	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool;

	/**
	 * Sets the general availability of the item
	 *
	 * @return bool $value True if available, false if not
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setAvailable( bool $value ) : \Aimeos\MShop\Common\Item\Iface;

	/**
	 * Tests if the item was modified.
	 *
	 * @return bool True if modified, false if not
	 */
	public function isModified() : bool;

	/**
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array $list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface;

	/**
	 * Returns an associative list of item properties.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array List of item properties
	 */
	public function toArray( bool $private = false ) : array;
}
