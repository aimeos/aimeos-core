<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	 * @param array $siteids List of IDs for sites Whose entries should be deleted
	 * @return null
	 */
	public function cleanup( array $siteids );

	/**
	 * Creates new item object.
	 *
	 * @return \Aimeos\MShop\Common\Item\Iface New item object
	 */
	public function createItem();

	/**
	 * Creates a search object.
	 *
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MW\Criteria\Iface Returns the Search object
	 */
	public function createSearch( $default = false );

	/**
	 * Deletes the item specified by its ID.
	 *
	 * @param mixed $id ID of the item object
	 * @return null
	 */
	public function deleteItem( $id );

	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 * @return null
	 */
	public function deleteItems( array $ids );

	/**
	 * Returns the item specified by its ID
	 *
	 * @param integer $id Id of item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function getItem( $id, array $ref = [], $default = false );

	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true );

	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true );

	/**
	 * Creates a new extension manager in the domain.
	 *
	 * @param string $domain Name of the domain (product, text, media, etc.)
	 * @param string|null $name Name of the implementation, will be from configuration (or Standard) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager extending the domain functionality
	 */
	public function getSubManager( $domain, $name = null );

	/**
	 * Adds or updates an item object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return null
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true );

	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Criteria object with conditions, sortations, etc.
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null );
}
