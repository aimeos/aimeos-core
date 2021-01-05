<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes() : array;

	/**
	 * Creates a new search object for storing search criterias.
	 *
	 * @return \Aimeos\MW\Criteria\Iface Search object instance
	 */
	public function createSearch() : \Aimeos\MW\Criteria\Iface;

	/**
	 * Creates a new node object.
	 *
	 * @return \Aimeos\MW\Tree\Node\Iface Empty node object
	 */
	public function createNode() : \Aimeos\MW\Tree\Node\Iface;

	/**
	 * Deletes a node and its descendants from the storage.
	 *
	 * @param string|null $id Delete the node with the ID and all nodes below
	 * @return \Aimeos\MW\Tree\Manager\Iface Manager object for method chaining
	 */
	public function deleteNode( string $id = null ) : Iface;

	/**
	 * Returns a node and its descendants depending on the given resource.
	 *
	 * @param string|null $id Retrieve nodes starting from the given ID
	 * @param int $level One of the level constants from \Aimeos\MW\Tree\Manager\Base
	 * @param \Aimeos\MW\Criteria\Iface|null $criteria Optional criteria object with conditions
	 * @return \Aimeos\MW\Tree\Node\Iface Node, maybe with subnodes
	 */
	public function getNode( string $id = null, int $level = Base::LEVEL_TREE, \Aimeos\MW\Criteria\Iface $criteria = null ) : \Aimeos\MW\Tree\Node\Iface;

	/**
	 * Inserts a new node before the given reference node to the parent in the storage.
	 *
	 * @param \Aimeos\MW\Tree\Node\Iface $node New node that should be inserted
	 * @param string|null $parentId ID of the parent node where the new node should be inserted below (null for root node)
	 * @param string|null $refId ID of the node where the node node should be inserted before (null to append)
	 * @return \Aimeos\MW\Tree\Node\Iface Updated node item
	 */
	public function insertNode( \Aimeos\MW\Tree\Node\Iface $node, string $parentId = null, string $refId = null ) : \Aimeos\MW\Tree\Node\Iface;

	/**
	 * Moves an existing node to the new parent in the storage.
	 *
	 * @param string $id ID of the node that should be moved
	 * @param string|null $oldParentId ID of the old parent node which currently contains the node that should be removed
	 * @param string|null $newParentId ID of the new parent node where the node should be moved to
	 * @param string|null $newRefId ID of the node where the node node should be inserted before (null to append)
	 * @return \Aimeos\MW\Tree\Manager\Iface Manager object for method chaining
	 */
	public function moveNode( string $id, string $oldParentId = null, string $newParentId = null, string $newRefId = null ) : Iface;

	/**
	 * Stores the values of the given node and it's descendants to the storage.
	 *
	 * This method does only store values like the node label but doesn't change
	 * the tree layout by adding, moving or deleting nodes.
	 *
	 * @param \Aimeos\MW\Tree\Node\Iface $node Node, maybe with subnodes
	 * @return \Aimeos\MW\Tree\Node\Iface Updated node item
	 */
	public function saveNode( \Aimeos\MW\Tree\Node\Iface $node ) : \Aimeos\MW\Tree\Node\Iface;

	/**
	 * Retrieves a list of nodes from the storage matching the given search criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @return \Aimeos\MW\Tree\Node\Iface[] List of tree nodes
	 */
	public function searchNodes( \Aimeos\MW\Criteria\Iface $search ) : array;

	/**
	 * Checks, whether a tree is read only.
	 *
	 * @return bool True if tree is read-only, false if not
	 */
	public function isReadOnly() : bool;

	/**
	 * Returns a list of node IDs that are in the path of given node ID
	 *
	 * @param string $id Id of node to get path
	 * @return \Aimeos\MW\Tree\Node\Iface[] List of tree nodes
	 */
	public function getPath( string $id ) : array;
}
