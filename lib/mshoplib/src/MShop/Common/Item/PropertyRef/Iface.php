<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\PropertyRef;


/**
 * Common interface for items containing property items.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface
{
	/**
	 * Adds a new property item or overwrite an existing one
	 *
	 * @param \Aimeos\MShop\Common\Item\Property\Iface $item New or existing property item
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 */
	public function addPropertyItem( \Aimeos\MShop\Common\Item\Property\Iface $item );

	/**
	 * Removes an existing property item
	 *
	 * @param \Aimeos\MShop\Common\Item\Property\Iface $item Existing property item
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 * @throws \Aimeos\MShop\Exception If given property item isn't found
	 */
	public function deletePropertyItem( \Aimeos\MShop\Common\Item\Property\Iface $item );

	/**
	 * Removes a list of existing property items
	 *
	 * @param \Aimeos\MShop\Common\Item\Property\Iface[] $items Existing property items
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 * @throws \Aimeos\MShop\Exception If a property item isn't found
	 */
	public function deletePropertyItems( array $items );

	/**
	 * Returns the deleted property items
	 *
	 * @return \Aimeos\MShop\Common\Item\Property\Iface[] Property items
	 */
	public function getPropertyItemsDeleted();

	/**
	 * Returns the property values for the given type
	 *
	 * @param string $type Type of the properties
	 * @return array List of property values
	 */
	public function getProperties( $type );

	/**
	 * Returns the property item for the given type, language and value
	 *
	 * @param string $type Name of the property type
	 * @param string $langId ISO language code (e.g. "en" or "en_US") or null if not language specific
	 * @param string $value Value of the property
	 * @param boolean $active True to return only active items, false to return all
	 * @return \Aimeos\MShop\Common\Item\Property\Iface|null Matching property item or null if none
	 */
	public function getPropertyItem( $type, $langId, $value, $active = true );

	/**
	 * Returns the property items of the product
	 *
	 * @param array|string|null $type Name of the property item type or null for all
	 * @param boolean $active True to return only active items, false to return all
	 * @return \Aimeos\MShop\Common\Item\Property\Iface[] Associative list of property IDs as keys and property items as values
	 */
	public function getPropertyItems( $type = null, $active = true );
}
