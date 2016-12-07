<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Tree
 */


namespace Aimeos\MW\Tree\Manager;


/**
 * Generic interface for all tree manager implementations.
 *
 * @package MW
 * @subpackage Tree
 */
interface Iface
{
	/**
	 * Returns a list of attributes which can be used in the search method.
	 *
	 * @return array List of search attribute objects implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes();

	/**
	 * Creates a new search object for storing search criterias.
	 *
	 * @return \Aimeos\MW\Criteria\Iface Search object instance
	 */
	public function createSearch();

	/**
	 * Creates a new node object.
	 *
	 * @return \Aimeos\MW\Tree\Node\Iface Empty node object
	 */
	public function createNode();

	/**
	 * Deletes a node and its descendants from the storage.
	 *
	 * @param mixed $id Delete the node with the ID and all nodes below
	 * @return null
	 */
	public function deleteNode( $id = null );

	/**
	 * Returns a node and its descendants depending on the given resource.
	 *
	 * @param integer|null $id Retrieve nodes starting from the given ID
	 * @param int $level One of the level constants from \Aimeos\MW\Tree\Manager\Base
	 * @param \Aimeos\MW\Criteria\Iface|null $criteria Optional criteria object with conditions
	 * @return \Aimeos\MW\Tree\Node\Iface Node, maybe with subnodes
	 */
	public function getNode( $id = null, $level = \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE, \Aimeos\MW\Criteria\Iface $criteria = null );

	/**
	 * Inserts a new node before the given reference node to the parent in the storage.
	 *
	 * @param \Aimeos\MW\Tree\Node\Iface $node New node that should be inserted
	 * @param integer|null $parentId ID of the parent node where the new node should be inserted below (null for root node)
	 * @param integer|null $refId ID of the node where the node node should be inserted before (null to append)
	 * @return null
	 */
	public function insertNode( \Aimeos\MW\Tree\Node\Iface $node, $parentId = null, $refId = null );

	/**
	 * Moves an existing node to the new parent in the storage.
	 *
	 * @param mixed $id ID of the node that should be moved
	 * @param mixed $oldParentId ID of the old parent node which currently contains the node that should be removed
	 * @param mixed $newParentId ID of the new parent node where the node should be moved to
	 * @param mixed $newRefId ID of the node where the node node should be inserted before (null to append)
	 * @return null
	 */
	public function moveNode( $id, $oldParentId, $newParentId, $newRefId = null );

	/**
	 * Stores the values of the given node and it's descendants to the storage.
	 *
	 * This method does only store values like the node label but doesn't change
	 * the tree layout by adding, moving or deleting nodes.
	 *
	 * @param \Aimeos\MW\Tree\Node\Iface $node Node, maybe with subnodes
	 * @return null
	 */
	public function saveNode( \Aimeos\MW\Tree\Node\Iface $node );

	/**
	 * Retrieves a list of nodes from the storage matching the given search criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @return array List of nodes implementing \Aimeos\MW\Tree\Node\Iface
	 */
	public function searchNodes( \Aimeos\MW\Criteria\Iface $search );

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
	 * @return array List of \Aimeos\MW\Tree\Node\Iface in Path with node id as key
	 */
	public function getPath( $id );
}