<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Common
 */


/**
 * Generic interface for all manager.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Manager_Iface
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
	 * @return MShop_Common_Item_Iface New item object
	 */
	public function createItem();

	/**
	 * Creates a search object.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Iface Returns the Search object
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
	 * @return MShop_Common_Item_Iface Item object
	 */
	public function getItem( $id, array $ref = array() );

	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Iface
	 */
	public function getSearchAttributes( $withsub = true );

	/**
	 * Creates a new extension manager in the domain.
	 *
	 * @return MShop_Common_Manager_Iface Manager extending the domain functionality
	 */
	public function getSubManager( $domain, $name = null );

	/**
	 * Adds or updates an item object.
	 *
	 * @param MShop_Common_Item_Iface $item Item object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Iface $item, $fetch = true );

	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param MW_Common_Criteria_Iface $search Criteria object with conditions, sortations, etc.
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Common_Item_Iface
	 */
	public function searchItems( MW_Common_Criteria_Iface $search, array $ref = array(), &$total = null );
}
