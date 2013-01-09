<?php

/**
 * @author Norbert Sendetzky <n.sendetzky@metaways.de>
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Tree
 * @version $Id: LDAP.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Tree manager managing LDAP directories.
 *
 * @package MW
 * @subpackage Tree
 */
class MW_Tree_Manager_LDAP extends MW_Tree_Manager_Abstract
{
	/**
	 * Initializes the tree manager.
	 *
	 * @param array Associative config array holding the configuration data
	 * @param mixed $resource Reference to the resource that should be used to manager the tree
	 */
	public function __construct( array $config, $resource )
	{
	}


	/**
	 * Returns a list of attributes which can be used in the search method.
	 *
	 * @return array List of search attribute objects implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes()
	{
		throw new MW_Tree_Exception( 'Not yet implemented' );
	}


	/**
	 * Creates a new search object for storing search criterias.
	 *
	 * @return MW_Common_Criteria_Interface Search object instance
	 */
	public function createSearch()
	{
		throw new MW_Tree_Exception( 'Not yet implemented' );
	}


	/**
	 * Creates a new node object.
	 *
	 * @return MW_Tree_Node_Interface Empty node object
	 */
	public function createNode()
	{
		throw new MW_Tree_Exception( 'Not yet implemented' );
	}


	/**
	 * Deletes a node and its descendants from the storage.
	 *
	 * @param mixed $relbase Relative base of the tree which can be an ID, a path, etc
	 */
	public function deleteNode( $relbase = null )
	{
		throw new MW_Tree_Exception( 'Not yet implemented' );
	}


	/**
	 * Returns a node and its descendants depending on the given resource.
	 *
	 * @param mixed $relbase Relative base of the tree which can be an ID, a path, etc
	 * @param int $level One of the level constants from MW_Tree_Manager_Abstract
	 * @param MW_Common_Criteria_Interface|null $criteria Optional criteria object with conditions
	 * @return MW_Tree_Node_Interface Node, maybe with subnodes
	 */
	public function getNode( $relbase = null, $level = MW_Tree_Manager_Abstract::LEVEL_TREE, MW_Common_Criteria_Interface $criteria = null )
	{
		throw new MW_Tree_Exception( 'Not yet implemented' );
	}

	/**
	 * Inserts a new node before the given reference node to the parent in the storage.
	 *
	 * @param MW_Tree_Node_Interface $node New node that should be inserted
	 * @param mixed $parentId ID of the parent node where the new node should be inserted below (null for root node)
	 * @param mixed $refId ID of the node where the node node should be inserted before (null to append)
	 */
	public function insertNode( MW_Tree_Node_Interface $node, $parentId = null, $refId = null )
	{
		throw new MW_Tree_Exception( 'Not yet implemented' );
	}


	/**
	 * Moves an existing node to the new parent in the storage.
	 *
	 * @param mixed $id ID of the node that should be moved
	 * @param mixed $oldParentId ID of the old parent node which currently contains the node that should be removed
	 * @param mixed $newParentId ID of the new parent node where the node should be moved to
	 * @param mixed $index Position in the list of children of the new parent where the node should be inserted
	 */
	public function moveNode( $id, $oldParentId, $newParentId, $index = 0 )
	{
		throw new MW_Tree_Exception( 'Not yet implemented' );
	}


	/**
	 * Stores the values of the given node and it's descendants to the storage.
	 *
	 * This method does only store values like the node label but doesn't change
	 * the tree layout by adding, moving or deleting nodes.
	 *
	 * @param MW_Tree_Node_Interface $node Node, maybe with subnodes
	 */
	public function saveNode( MW_Tree_Node_Interface $node )
	{
		throw new MW_Tree_Exception( 'Not yet implemented' );
	}


	/**
	 * Retrieves a list of nodes from the storage matching the given search criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria object
	 * @param mixed $relbase Relative base of the tree which can be an ID, a path, etc
	 * @return array List of nodes implementing MW_Tree_Node_Interface
	 */
	public function searchNodes( MW_Common_Criteria_Interface $search, $relbase = null )
	{
		throw new MW_Tree_Exception( 'Not yet implemented' );
	}


	/**
	 * Returns a list if node ids, that are in the path of given node id
	 *
	 * @param mixed $id Id of node to get path
	 * @return array List of Ids in Path in given node (id)
	 */
	public function getPath( $id )
	{
		throw new MW_Tree_Exception( 'Not yet implemented' );
	}
}
