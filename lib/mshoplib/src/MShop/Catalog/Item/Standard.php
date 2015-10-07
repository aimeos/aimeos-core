<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Catalog
 */


namespace Aimeos\MShop\Catalog\Item;


/**
 * Generic interface for catalog items.
 *
 * @package MShop
 * @subpackage Catalog
 */
class Standard
	extends \Aimeos\MShop\Common\Item\ListRef\Base
	implements \Aimeos\MShop\Catalog\Item\Iface
{
	private $node;
	private $children;


	/**
	 * Initializes the catalog item.
	 *
	 * @param \Aimeos\MW\Tree\Node\Iface $node Tree node
	 * @param \Aimeos\MShop\Catalog\Item\Iface[] $children List of children of the item
	 * @param \Aimeos\MShop\Common\Lists\Item\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 */
	public function __construct( \Aimeos\MW\Tree\Node\Iface $node, array $children = array(),
		array $listItems = array(), array $refItems = array() )
	{
		parent::__construct( '', array(), $listItems, $refItems );

		\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MShop\\Catalog\\Item\\Iface', $children );

		$this->children = $children;
		$this->node = $node;
	}


	/**
	 * Clones internal objects of the catalog item.
	 */
	public function __clone()
	{
		$this->node = clone $this->node;
	}


	/**
	 * Returns the unique ID of the node.
	 *
	 * @return string|null Unique ID of the node
	 */
	public function getId()
	{
		return $this->node->getId();
	}


	/**
	 * Sets the unique ID of the node.
	 *
	 * @param string|null Unique ID of the node
	 */
	public function setId( $id )
	{
		if( $id === $this->getId() ) { return; }

		$this->node->setId( $id );
	}


	/**
	 * Returns the site ID of the item.
	 *
	 * @return integer|null Site ID of the item
	 */
	public function getSiteId()
	{
		return $this->node->__get( 'siteid' );
	}


	/**
	 * Returns the internal name of the item.
	 *
	 * @return string Name of the item
	 */
	public function getLabel()
	{
		return $this->node->getLabel();
	}


	/**
	 * Sets the new internal name of the item.
	 *
	 * @param string $name New name of the item
	 */
	public function setLabel( $name )
	{
		if( $name == $this->getLabel() ) { return; }

		$this->node->setLabel( $name );
	}


	/**
	 * Returns the config property of the catalog.
	 *
	 * @return array Returns the config of the catalog node
	 */
	public function getConfig()
	{
		return $this->node->__isset( 'config' ) && is_array( $this->node->config ) ? $this->node->__get( 'config' ) : array();
	}


	/**
	 * Sets the config property of the catalog item.
	 *
	 * @param array $options Options to be set for the catalog node
	 */
	public function setConfig( array $options )
	{
		$this->node->__set( 'config', $options );
	}


	/**
	 * Returns the code of the item.
	 *
	 * @return string Code of the item
	 */
	public function getCode()
	{
		return $this->node->getCode();
	}


	/**
	 * Sets the new code of the item.
	 *
	 * @param string $name New code of the item
	 */
	public function setCode( $name )
	{
		$this->checkCode( $name );

		$this->node->setCode( $name );
	}


	/**
	 * Returns the status of the item.
	 *
	 * @return integer Greater than zero if enabled, zero or negative values if disabled
	 */
	public function getStatus()
	{
		return $this->node->getStatus();
	}

	/**
	 * Sets the new status of the item.
	 *
	 * @param integer $status True if enabled, false if not
	 */
	public function setStatus( $status )
	{
		if( $status === $this->getStatus() ) { return; }

		$this->node->setStatus( $status );
	}

	/**
	 * Returns modification time of the order item base product.
	 *
	 * @return string Returns modification time of the order base item
	 */
	public function getTimeModified()
	{
		return $this->node->__get( 'mtime' );
	}


	/**
	 * Returns the create date of the item.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeCreated()
	{
		return $this->node->__get( 'ctime' );
	}


	/**
	 * Returns the editor code of editor who created/modified the item at last.
	 *
	 * @return string Editorcode of editor who created/modified the item at last
	 */
	public function getEditor()
	{
		return $this->node->__get( 'editor' );
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
				case 'catalog.id': $this->node->setId( $value ); break;
				case 'catalog.code': $this->node->setCode( $value ); break;
				case 'catalog.label': $this->node->setLabel( $value ); break;
				case 'catalog.status': $this->node->setStatus( $value ); break;
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
			'catalog.id' => $this->node->getId(),
			'catalog.code' => $this->node->getCode(),
			'catalog.label' => $this->node->getLabel(),
			'catalog.status' => $this->node->getStatus(),
			'catalog.config' => $this->getConfig(),
			'catalog.siteid' => $this->node->__get( 'siteid' ),
			'catalog.ctime' => $this->node->__get( 'ctime' ),
			'catalog.mtime' => $this->node->__get( 'mtime' ),
			'catalog.editor' => $this->node->__get( 'editor' ),
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
		return $this->node->isModified();
	}

	/**
	 * Returns a child of this node identified by its index.
	 *
	 * @param integer $index Index of child node
	 * @return \Aimeos\MShop\Catalog\Item\Iface Selected node
	 */
	public function getChild( $index )
	{
		if( isset( $this->children[$index] ) ) {
			return $this->children[$index];
		}

		throw new \Aimeos\MShop\Catalog\Exception( sprintf( 'Child node with index "%1$d" not available', $index ) );
	}

	/**
	 * Returns all children of this node.
	 *
	 * @return array Numerically indexed list of nodes
	 */
	public function getChildren()
	{
		return $this->children;
	}

	/**
	 * Tests if a node has children.
	 *
	 * @return boolean True if node has children, false if not
	 */
	public function hasChildren()
	{
		if( count( $this->children ) > 0 ) {
			return true;
		}

		return $this->node->hasChildren();
	}

	/**
	 * Adds a child node to this node.
	 *
	 * @param \Aimeos\MShop\Catalog\Item\Iface $item Child node to add
	 */
	public function addChild( \Aimeos\MShop\Catalog\Item\Iface $item )
	{
		// don't set the modified flag as it's only for the values
		$this->children[] = $item;
	}


	/**
	 * Returns the internal node.
	 *
	 * @return \Aimeos\MW\Tree\Node\Iface Internal node object
	 */
	public function getNode()
	{
		return $this->node;
	}
}
