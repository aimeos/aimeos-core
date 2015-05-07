<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
 */


/**
 * Generic interface for catalog items.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Item_Default
	extends MShop_Common_Item_ListRef_Abstract
	implements MShop_Catalog_Item_Interface
{
	private $_node;
	private $_children;


	/**
	 * Initializes the catalog item.
	 *
	 * @param MW_Tree_Node_Interface $node Tree node
	 * @param array $listItems List of list items
	 * @param array $refItems List of referenced items
	 */
	public function __construct( MW_Tree_Node_Interface $node, array $children = array(),
		array $listItems = array(), array $refItems = array() )
	{
		parent::__construct( '', array(), $listItems, $refItems );

		MW_Common_Abstract::checkClassList( 'MShop_Catalog_Item_Interface', $children );

		$this->_children = $children;
		$this->_node = $node;
	}


	/**
	 * Clones internal objects of the catalog item.
	 */
	public function __clone()
	{
		$this->_node = clone $this->_node;
	}


	/**
	 * Returns the unique ID of the node.
	 *
	 * @return string|null Unique ID of the node
	 */
	public function getId()
	{
		return $this->_node->getId();
	}


	/**
	 * Sets the unique ID of the node.
	 *
	 * @param string|null Unique ID of the node
	 */
	public function setId( $id )
	{
		if ( $id === $this->getId() ) { return; }

		$this->_node->setId( $id );
	}


	/**
	 * Returns the site ID of the item.
	 *
	 * @return integer|null Site ID of the item
	 */
	public function getSiteId()
	{
		return $this->_node->__get('siteid');
	}


	/**
	 * Returns the internal name of the item.
	 *
	 * @return string Name of the item
	 */
	public function getLabel()
	{
		return $this->_node->getLabel();
	}


	/**
	 * Sets the new internal name of the item.
	 *
	 * @param string $name New name of the item
	 */
	public function setLabel( $name )
	{
		if ( $name == $this->getLabel() ) { return; }

		$this->_node->setLabel( $name );
	}


	/**
	 * Returns the config property of the catalog.
	 *
	 * @return array Returns the config of the catalog node
	 */
	public function getConfig()
	{
		return $this->_node->__isset( 'config' ) && is_array( $this->_node->config ) ? $this->_node->__get( 'config' ) : array();
	}


	/**
	 * Sets the config property of the catalog item.
	 *
	 * @param array $options Options to be set for the catalog node
	 */
	public function setConfig( array $options )
	{
		$this->_node->__set( 'config', $options );
	}


	/**
	 * Returns the code of the item.
	 *
	 * @return string Code of the item
	 */
	public function getCode()
	{
		return $this->_node->getCode();
	}


	/**
	 * Sets the new code of the item.
	 *
	 * @param string $name New code of the item
	 */
	public function setCode( $name )
	{
		$this->_checkCode( $name );

		$this->_node->setCode( $name );
	}


	/**
	 * Returns the status of the item.
	 *
	 * @return integer Greater than zero if enabled, zero or negative values if disabled
	 */
	public function getStatus()
	{
		return $this->_node->getStatus();
	}

	/**
	 * Sets the new status of the item.
	 *
	 * @param integer $status True if enabled, false if not
	 */
	public function setStatus( $status )
	{
		if ( $status === $this->getStatus() ) { return; }

		$this->_node->setStatus( $status );
	}

	/**
	 * Returns modification time of the order item base product.
	 *
	 * @return string Returns modification time of the order base item
	 */
	public function getTimeModified()
	{
		return $this->_node->__get('mtime');
	}


	/**
	 * Returns the create date of the item.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeCreated()
	{
		return $this->_node->__get('ctime');
	}


	/**
	 * Returns the editor code of editor who created/modified the item at last.
	 *
	 * @return string Editorcode of editor who created/modified the item at last
	 */
	public function getEditor()
	{
		return $this->_node->__get('editor');
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = array();
		$list = parent::fromArray( $list );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'catalog.id': $this->_node->setId( $value ); break;
				case 'catalog.code': $this->_node->setCode( $value ); break;
				case 'catalog.label': $this->_node->setLabel( $value ); break;
				case 'catalog.status': $this->_node->setStatus( $value ); break;
				case 'catalog.config': $this->setConfig( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the public values of the node as array.
	 *
	 * @return array Assciative list of key/value pairs
	 */
	public function toArray()
	{
		return array(
			'catalog.id' => $this->_node->getId(),
			'catalog.code' => $this->_node->getCode(),
			'catalog.label' => $this->_node->getLabel(),
			'catalog.status' => $this->_node->getStatus(),
			'catalog.config' => $this->getConfig(),
			'catalog.siteid' => $this->_node->__get('siteid'),
			'catalog.ctime' => $this->_node->__get('ctime'),
			'catalog.mtime' => $this->_node->__get('mtime'),
			'catalog.editor' => $this->_node->__get('editor'),
			'catalog.hasChildren' => $this->hasChildren()
		);
	}


	/**
	 * Checks, whether this node was modified.
	 *
	 * @return boolean True if the content of the node is modified, false if not
	 */
	public function isModified()
	{
		return $this->_node->isModified();
	}

	/**
	 * Returns a child of this node identified by its index.
	 *
	 * @param integer $index Index of child node
	 * @return MShop_Catalog_Item_Interface Selected node
	 */
	public function getChild( $index )
	{
		if( isset( $this->_children[$index] ) ) {
			return $this->_children[$index];
		}

		throw new MShop_Catalog_Exception( sprintf( 'Child node with index "%1$d" not available', $index ) );
	}

	/**
	 * Returns all children of this node.
	 *
	 * @return array Numerically indexed list of nodes
	 */
	public function getChildren()
	{
		return $this->_children;
	}

	/**
	 * Tests if a node has children.
	 *
	 * @return boolean True if node has children, false if not
	 */
	public function hasChildren()
	{
		if( count( $this->_children ) > 0 ) {
			return true;
		}

		return $this->_node->hasChildren();
	}

	/**
	 * Adds a child node to this node.
	 *
	 * @param MShop_Catalog_Item_Interface $item Child node to add
	 */
	public function addChild( MShop_Catalog_Item_Interface $item )
	{
		// don't set the modified flag as it's only for the values
		$this->_children[] = $item;
	}


	/**
	 * Returns the internal node.
	 *
	 * @return MW_Tree_Node_Interface Internal node object
	 */
	public function getNode()
	{
		return $this->_node;
	}
}
