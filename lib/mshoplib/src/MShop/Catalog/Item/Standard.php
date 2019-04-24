<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Catalog\Item\Iface
{
	use \Aimeos\MShop\Common\Item\Config\Traits;
	use \Aimeos\MShop\Common\Item\ListRef\Traits;


	private $node;
	private $children;
	private $deletedItems = [];


	/**
	 * Initializes the catalog item.
	 *
	 * @param \Aimeos\MW\Tree\Node\Iface $node Tree node
	 * @param \Aimeos\MShop\Catalog\Item\Iface[] $children List of children of the item
	 * @param \Aimeos\MShop\Common\Lists\Item\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 */
	public function __construct( \Aimeos\MW\Tree\Node\Iface $node, array $children = [],
		array $listItems = [], array $refItems = [] )
	{
		parent::__construct( '', [] );

		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Catalog\Item\Iface::class, $children );

		$this->initListItems( $listItems, $refItems );
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
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'catalog';
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
	 * @param string|null $id Unique ID of the node
	 * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
	 */
	public function setId( $id )
	{
		$this->node->setId( $id );
		return $this;
	}


	/**
	 * Returns the site ID of the item.
	 *
	 * @return string|null Site ID of the item
	 */
	public function getSiteId()
	{
		return ( $this->node->__isset( 'siteid' ) ? (string) $this->node->__get( 'siteid' ) : null );
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
	 * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
	 */
	public function setLabel( $name )
	{
		$this->node->setLabel( $name );
		return $this;
	}


	/**
	 * Returns the config property of the catalog.
	 *
	 * @return array Returns the config of the catalog node
	 */
	public function getConfig()
	{
		return ( $this->node->__isset( 'config' ) ? (array) $this->node->__get( 'config' ) : [] );
	}


	/**
	 * Sets the config property of the catalog item.
	 *
	 * @param array $options Options to be set for the catalog node
	 * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
	 */
	public function setConfig( array $options )
	{
		$this->node->config = $options;
		return $this;
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
	 * @param string $code New code of the item
	 * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
	 */
	public function setCode( $code )
	{
		$this->node->setCode( $this->checkCode( $code ) );
		return $this;
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
	 * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
	 */
	public function setStatus( $status )
	{
		$this->node->setStatus( $status );
		return $this;
	}


	/**
	 * Returns the URL target specific for that category
	 *
	 * @return string URL target specific for that category
	 */
	public function getTarget()
	{
		return ( $this->node->__isset( 'target' ) ? (string) $this->node->__get( 'target' ) : '' );
	}


	/**
	 * Sets a new URL target specific for that category
	 *
	 * @param string $value New URL target specific for that category
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setTarget( $value )
	{
		$this->node->target = (string) $value;
		return $this;
	}


	/**
	 * Returns modify date/time of the order item base product.
	 *
	 * @return string Returns modify date/time of the order base item
	 */
	public function getTimeModified()
	{
		return ( $this->node->__isset( 'mtime' ) ? $this->node->__get( 'mtime' ) : null );
	}


	/**
	 * Returns the create date of the item.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeCreated()
	{
		return ( $this->node->__isset( 'ctime' ) ? $this->node->__get( 'ctime' ) : null );
	}


	/**
	 * Returns the editor code of editor who created/modified the item at last.
	 *
	 * @return string Editor who created/modified the item at last
	 */
	public function getEditor()
	{
		return ( $this->node->__isset( 'editor' ) ? $this->node->__get( 'editor' ) : null );
	}


	/**
	 * Adds a child node to this node.
	 *
	 * @param \Aimeos\MShop\Common\Item\Tree\Iface $item Child node to add
	 * @return \Aimeos\MShop\Common\Item\Tree\Iface Tree item for chaining method calls
	 */
	public function addChild( \Aimeos\MShop\Common\Item\Tree\Iface $item )
	{
		// don't set the modified flag as it's only for the values
		$this->children[] = $item;

		return $this;
	}


	/**
	 * Removes a child node from this node.
	 *
	 * @param \Aimeos\MShop\Common\Item\Tree\Iface $item Child node to remove
	 * @return \Aimeos\MShop\Common\Item\Tree\Iface Tree item for chaining method calls
	 */
	public function deleteChild( \Aimeos\MShop\Common\Item\Tree\Iface $item )
	{
		foreach( $this->children as $idx => $child )
		{
			if( $child === $item )
			{
				$this->deletedItems[] = $item;
				unset( $this->children[$idx] );
			}
		}

		return $this;
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
	 * Returns the deleted children.
	 *
	 * @return \Aimeos\MShop\Catalog\Item\Iface[] List of removed children
	 */
	public function getChildrenDeleted()
	{
		return $this->deletedItems;
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
	 * Returns the internal node.
	 *
	 * For internal use only!
	 *
	 * @return \Aimeos\MW\Tree\Node\Iface Internal node object
	 */
	public function getNode()
	{
		return $this->node;
	}


	/**
	 * Returns the level of the item in the tree
	 *
	 * For internal use only!
	 *
	 * @return integer Level of the item starting with "0" for the root node
	 */
	public function getLevel()
	{
		return ( $this->node->__isset( 'level' ) ? $this->node->__get( 'level' ) : 0 );
	}


	/**
	 * Returns the ID of the parent category
	 *
	 * For internal use only!
	 *
	 * @return string|null Unique ID of the parent category
	 */
	public function getParentId()
	{
		return ( $this->node->__isset( 'parentid' ) ? $this->node->__get( 'parentid' ) : null );
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return boolean True if available, false if not
	 */
	public function isAvailable()
	{
		return $this->getStatus() > 0;
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


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'catalog.code': $item = $item->setCode( $value ); break;
				case 'catalog.label': $item = $item->setLabel( $value ); break;
				case 'catalog.status': $item = $item->setStatus( $value ); break;
				case 'catalog.config': $item = $item->setConfig( $value ); break;
				case 'catalog.target': $item = $item->setTarget( $value ); break;
				case 'catalog.id': !$private ?: $item = $item->setId( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the public values of the node as array.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Assciative list of key/value pairs
	 */
	public function toArray( $private = false )
	{
		$list = [
			'catalog.code' => $this->getCode(),
			'catalog.label' => $this->getLabel(),
			'catalog.config' => $this->getConfig(),
			'catalog.status' => $this->getStatus(),
			'catalog.target' => $this->getTarget(),
			'catalog.hasChildren' => $this->hasChildren(),
		];

		if( $private === true )
		{
			$list['catalog.id'] = $this->getId();
			$list['catalog.level'] = $this->getLevel();
			$list['catalog.siteid'] = $this->getSiteId();
			$list['catalog.parentid'] = $this->getParentId();
			$list['catalog.ctime'] = $this->getTimeCreated();
			$list['catalog.mtime'] = $this->getTimeModified();
			$list['catalog.editor'] = $this->getEditor();
		}

		return $list;
	}


	/**
	 * Returns the node and its children as list
	 *
	 * @return \Aimeos\MShop\Catalog\Item\Iface Associative list of IDs as keys and nodes as values
	 */
	public function toList()
	{
		$list = [$this->getId() => $this];

		foreach( $this->getChildren() as $child ) {
			$list += $child->toList();
		}

		return $list;
	}
}
