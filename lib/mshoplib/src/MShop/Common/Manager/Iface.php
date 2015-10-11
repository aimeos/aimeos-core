<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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
	 * @return void
	 */
	public function deleteItem( $id );

	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids );

	/**
	 * Returns the item specified by its ID
	 *
	 * @param integer $id Id of item
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function getItem( $id, array $ref = array() );

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
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager extending the domain functionality
	 */
	public function getSubManager( $domain, $name = null );

	/**
	 * Adds or updates an item object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
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
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = array(), &$total = null );
}
