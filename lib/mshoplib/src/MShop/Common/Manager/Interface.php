<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Generic interface for all manager.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Manager_Interface
{
	/**
	 * Creates new item object.
	 *
	 * @return MShop_Common_Item_Interface New item object
	 */
	public function createItem();

	/**
	 * Creates a search object.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Interface Returns the Search object
	 */
	public function createSearch($default = false);

	/**
	 * Deletes the item specified by its ID.
	 *
	 * @param mixed $id ID of the item object
	 */
	public function deleteItem( $id );

	/**
	 * Returns the item specified by its ID
	 *
	 * @param integer $id Id of item
	 * @return MShop_Common_Item_Interface Item object
	 */
	public function getItem( $id, array $ref = array() );

	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true );

	/**
	 * Creates a new extension manager in the domain.
	 *
	 * @return MShop_Common_Manager_Interface Manager extending the domain functionality
	 */
	public function getSubManager( $domain, $name = null );

	/**
	 * Adds or updates an item object.
	 *
	 * @param MShop_Common_Item_Interface $item Item object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true );

	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param MW_Common_Criteria_Interface $search Criteria object with conditions, sortations, etc.
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Common_Item_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null );
}
