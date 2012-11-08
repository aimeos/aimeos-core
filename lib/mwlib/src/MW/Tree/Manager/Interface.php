<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Tree
 * @version $Id: Interface.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Generic interface for all tree manager implementations.
 *
 * @package MW
 * @subpackage Tree
 */
interface MW_Tree_Manager_Interface
{
	/**
	 * Returns a list of attributes which can be used in the search method.
	 *
	 * @return array List of search attribute objects implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes();

	/**
	 * Creates a new search object for storing search criterias.
	 *
	 * @return MW_Common_Criteria_Interface Search object instance
	 */
	public function createSearch();

	/**
	 * Creates a new node object.
	 *
	 * @return MW_Tree_Node_Interface Empty node object
	 */
	public function createNode();

	/**
	 * Deletes a node and its descendants from the storage.
	 *
	 * @param mixed $id Delete the node with the ID and all nodes below
	 */
	public function deleteNode( $id = null );

	/**
	 * Returns a node and its descendants depending on the given resource.
	 *
	 * @param mixed $id Retrieve nodes starting from the given ID
	 * @param int $level One of the level constants from MW_Tree_Manager_Abstract
	 * @return MW_Tree_Node_Interface Node, maybe with subnodes
	 */
	public function getNode( $id = null, $level = MW_Tree_Manager_Abstract::LEVEL_TREE );

	/**
	 * Inserts a new node before the given reference node to the parent in the storage.
	 *
	 * @param MW_Tree_Node_Interface $node New node that should be inserted
	 * @param mixed $parentId ID of the parent node where the new node should be inserted below (null for root node)
	 * @param mixed $refId ID of the node where the node node should be inserted before (null to append)
	 */
	public function insertNode( MW_Tree_Node_Interface $node, $parentId = null, $refId = null );

	/**
	 * Moves an existing node to the new parent in the storage.
	 *
	 * @param mixed $id ID of the node that should be moved
	 * @param mixed $oldParentId ID of the old parent node which currently contains the node that should be removed
	 * @param mixed $newParentId ID of the new parent node where the node should be moved to
	 * @param mixed $newRefId ID of the node where the node node should be inserted before (null to append)
	 */
	public function moveNode( $id, $oldParentId, $newParentId, $newRefId = null );

	/**
	 * Stores the values of the given node and it's descendants to the storage.
	 *
	 * This method does only store values like the node label but doesn't change
	 * the tree layout by adding, moving or deleting nodes.
	 *
	 * @param MW_Tree_Node_Interface $node Node, maybe with subnodes
	 */
	public function saveNode( MW_Tree_Node_Interface $node );

	/**
	 * Retrieves a list of nodes from the storage matching the given search criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria object
	 * @return array List of nodes implementing MW_Tree_Node_Interface
	 */
	public function searchNodes( MW_Common_Criteria_Interface $search );

	/**
	 * Checks, whether a tree is read only.
	 *
	 * @return boolean True if tree is read-only, false if not
	 */
	public function isReadOnly();
	
	/**
	 * Returns a list if node ids, that are in the path of given node id
	 * 
	 * @param mixed $id Id of node to get path
	 * @return array List of MW_Tree_Node_Interface in Path with node id as key
	 */
	public function getPath( $id );
}