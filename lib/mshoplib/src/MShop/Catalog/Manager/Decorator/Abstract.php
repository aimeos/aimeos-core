<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
 * @version $Id: Abstract.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Provides common methods for catalog manager decorators.
 *
 * @package MShop
 * @subpackage Catalog
 */
abstract class MShop_Catalog_Manager_Decorator_Abstract
	extends MShop_Common_Manager_Decorator_Abstract
	implements MShop_Common_Manager_Decorator_Interface, MShop_Catalog_Manager_Interface
{
	/**
	 * Returns a list if item IDs, that are in the path of given item ID.
	 *
	 * @param integer $id ID of item to get the path for
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return array Associative list of items implementing MShop_Catalog_Item_Interface with IDs as keys
	 */
	public function getPath( $id, array $ref = array() )
	{
		return $this->_getManager()->getPath( $id, $ref );
	}


	/**
	 * Returns a node and its descendants depending on the given resource.
	 *
	 * @param integer|null $id Retrieve nodes starting from the given ID
	 * @param array List of domains (e.g. text, media, etc.) whose referenced items should be attached to the objects
	 * @param integer $level One of the level constants from MW_Tree_Manager_Abstract
	 * @return MW_Tree_Node_Interface Node, maybe with subnodes
	 */
	public function getTree( $id = null, array $ref = array(), $level = MW_Tree_Manager_Abstract::LEVEL_TREE )
	{
		return $this->_getManager()->getTree( $id, $ref, $level );
	}


	/**
	 * Adds a new item object.
	 *
	 * @param MShop_Common_Item_Interface $item Item which should be inserted
	 */
	public function insertItem( MShop_Catalog_Item_Interface $item, $parentId = null, $refId = null )
	{
		$this->_getManager()->insertItem( $item, $parentId, $refId );
	}


	/**
	 * Moves an existing item to the new parent in the storage.
	 *
	 * @param mixed $id ID of the item that should be moved
	 * @param mixed $oldParentId ID of the old parent item which currently contains the item that should be removed
	 * @param mixed $newParentId ID of the new parent item where the item should be moved to
	 * @param mixed $newRefId ID of the item where the item should be inserted before (null to append)
	 */
	public function moveItem( $id, $oldParentId, $newParentId, $refId = null )
	{
		$this->_getManager()->moveItem( $id, $oldParentId, $newParentId, $refId );
	}


	/**
	 * Rebuild the catalog index for searching products.
	 * This can be a long lasting operation.
	 */
	public function rebuildIndex()
	{
		$this->_getManager()->rebuildIndex();
	}
}
