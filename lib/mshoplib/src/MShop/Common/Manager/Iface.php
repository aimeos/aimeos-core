<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager;


/**
 * Generic interface for all manager.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Removes old entries from the storage
	 *
	 * @param string[] $siteids List of IDs for sites Whose entries should be deleted
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( array $siteids ) : \Aimeos\MShop\Common\Manager\Iface;

	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Iface New item object
	 */
	public function createItem( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface;

	/**
	 * Creates a search object.
	 *
	 * @param bool $default Add default criteria
	 * @return \Aimeos\MW\Criteria\Iface Returns the Search object
	 */
	public function createSearch( bool $default = false ) : \Aimeos\MW\Criteria\Iface;

	/**
	 * Deletes the item.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface|string $itemId Item object or ID of the item object
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function deleteItem( $itemId ) : \Aimeos\MShop\Common\Manager\Iface;

	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function deleteItems( array $itemIds ) : \Aimeos\MShop\Common\Manager\Iface;

	/**
	 * Returns the item specified by its ID
	 *
	 * @param string $id Id of item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool $default Add default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function getItem( string $id, array $ref = [], bool $default = false ) : \Aimeos\MShop\Common\Item\Iface;

	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array;

	/**
	 * Returns the attributes used for saving column values.
	 *
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of attribute items
	 */
	public function getSaveAttributes() : array;

	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array;

	/**
	 * Creates a new extension manager in the domain.
	 *
	 * @param string $domain Name of the domain (product, text, media, etc.)
	 * @param string|null $name Name of the implementation, will be from configuration (or Standard) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager extending the domain functionality
	 */
	public function getSubManager( string $domain, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface;

	/**
	 * Adds or updates a list of item objects.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[] $items List of item object whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface[] Saved item objects
	 */
	public function saveItems( array $items, bool $fetch = true ) : array;

	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Criteria object with conditions, sortations, etc.
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int &$total Number of items that are available in total
	 * @return \Aimeos\MShop\Common\Item\Iface[] List of items
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : array;

	/**
	 * Injects the reference of the outmost object
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $object Reference to the outmost manager or decorator
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Common\Manager\Iface $object ) : \Aimeos\MShop\Common\Manager\Iface;

	/**
	 * Starts a database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function begin() : \Aimeos\MShop\Common\Manager\Iface;

	/**
	 * Commits the running database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function commit() : \Aimeos\MShop\Common\Manager\Iface;

	/**
	 * Rolls back the running database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function rollback() : \Aimeos\MShop\Common\Manager\Iface;
}
