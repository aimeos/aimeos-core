<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Tree
 */


/**
 * Generic interface for all tree manager implementations.
 *
 * @package MW
 * @subpackage Tree
 */
interface MW_Tree_Manager_Iface
{
	/**
	 * Returns a list of attributes which can be used in the search method.
	 *
	 * @return array List of search attribute objects implementing MW_Common_Criteria_Attribute_Iface
	 */
	public function getSearchAttributes();

	/**
	 * Creates a new search object for storing search criterias.
	 *
	 * @return MW_Common_Criteria_Iface Search object instance
	 */
	public function createSearch();

	/**
	 * Creates a new node object.
	 *
	 * @return MW_Tree_Node_Iface Empty node object
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
	 * @param integer|null $id Retrieve nodes starting from the given ID
	 * @param int $level One of the level constants from MW_Tree_Manager_Base
	 * @param MW_Common_Criteria_Iface|null $criteria Optional criteria object with conditions
	 * @return MW_Tree_Node_Iface Node, maybe with subnodes
	 */
	public function getNode( $id = null, $level = MW_Tree_Manager_Base::LEVEL_TREE, MW_Common_Criteria_Iface $criteria = null );

	/**
	 * Inserts a new node before the given reference node to the parent in the storage.
	 *
	 * @param MW_Tree_Node_Iface $node New node that should be inserted
	 * @param integer|null $parentId ID of the parent node where the new node should be inserted below (null for root node)
	 * @param integer|null $refId ID of the node where the node node should be inserted before (null to append)
	 */
	public function insertNode( MW_Tree_Node_Iface $node, $parentId = null, $refId = null );

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
	 * @param MW_Tree_Node_Iface $node Node, maybe with subnodes
	 */
	public function saveNode( MW_Tree_Node_Iface $node );

	/**
	 * Retrieves a list of nodes from the storage matching the given search criteria.
	 *
	 * @param MW_Common_Criteria_Iface $search Search criteria object
	 * @return array List of nodes implementing MW_Tree_Node_Iface
	 */
	public function searchNodes( MW_Common_Criteria_Iface $search );

	/**
	 * Checks, whether a tree is read only.
	 *
	 * @return boolean True if tree is read-only, false if not
	 */
	public function isReadOnly();

	/**
	 * Returns a list if node ids, that are in the path of given node id
	 *
	 * @param integer $id Id of node to get path
	 * @return array List of MW_Tree_Node_Iface in Path with node id as key
	 */
	public function getPath( $id );
}