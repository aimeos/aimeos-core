<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Catalog
 */


/**
 * Shop catalog interface with methods for managing categories, groups and products.
 * @package MShop
 * @subpackage Catalog
 */
interface MShop_Catalog_Manager_Interface
{
	/**
	 * Returns a list of item IDs, that are in the path of given item ID.
	 *
	 * @param integer $id ID of item to get the path for
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return array Associative list of items implementing MShop_Catalog_Item_Interface with IDs as keys
	 */
	public function getPath( $id, array $ref = array() );


	/**
	 * Returns a node and its descendants depending on the given resource.
	 *
	 * @param integer|null $id Retrieve nodes starting from the given ID
	 * @param array List of domains (e.g. text, media, etc.) whose referenced items should be attached to the objects
	 * @param integer $level One of the level constants from MW_Tree_Manager_Abstract
	 * @param MW_Common_Criteria_Interface|null $criteria Optional criteria object with conditions
	 * @return MShop_Catalog_Item_Interface Catalog item, maybe with subnodes
	 */
	public function getTree( $id = null, array $ref = array(), $level = MW_Tree_Manager_Abstract::LEVEL_TREE, MW_Common_Criteria_Interface $criteria = null );


	/**
	 * Adds a new item object.
	 *
	 * @param MShop_Catalog_Item_Interface $item Item which should be inserted
	 * @param integer $parentId ID of the parent item where the item should be inserted into
	 * @param integer $refId ID of the item where the item should be inserted before (null to append)
	 * @return void
	 */
	public function insertItem( MShop_Catalog_Item_Interface $item, $parentId = null, $refId = null );


	/**
	 * Moves an existing item to the new parent in the storage.
	 *
	 * @param mixed $id ID of the item that should be moved
	 * @param mixed $oldParentId ID of the old parent item which currently contains the item that should be removed
	 * @param mixed $newParentId ID of the new parent item where the item should be moved to
	 * @param mixed $refId ID of the item where the item should be inserted before (null to append)
	 * @return void
	 */
	public function moveItem( $id, $oldParentId, $newParentId, $refId = null );
}
