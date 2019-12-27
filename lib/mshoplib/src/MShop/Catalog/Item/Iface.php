<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Config\Iface,
		\Aimeos\MShop\Common\Item\ListRef\Iface, \Aimeos\MShop\Common\Item\Tree\Iface
{
	/**
	 * Returns the URL target specific for that category
	 *
	 * @return string URL target specific for that category
	 */
	public function getTarget() : string;

	/**
	 * Sets a new URL target specific for that category
	 *
	 * @param string $value New URL target specific for that category
	 * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
	 */
	public function setTarget( string $value ) : \Aimeos\MShop\Catalog\Item\Iface;

	/**
	 * Adds a child node to this node.
	 *
	 * @param \Aimeos\MShop\Common\Item\Tree\Iface $item Child node to add
	 * @return \Aimeos\MShop\Common\Item\Tree\Iface Tree item for chaining method calls
	 */
	public function addChild( \Aimeos\MShop\Common\Item\Tree\Iface $item ) : \Aimeos\MShop\Common\Item\Tree\Iface;

	/**
	 * Removes a child node from this node.
	 *
	 * @param \Aimeos\MShop\Common\Item\Tree\Iface $item Child node to remove
	 * @return \Aimeos\MShop\Common\Item\Tree\Iface Tree item for chaining method calls
	 */
	public function deleteChild( \Aimeos\MShop\Common\Item\Tree\Iface $item ) : \Aimeos\MShop\Common\Item\Tree\Iface;

	/**
	 * Returns a child of this node identified by its index.
	 *
	 * @param int $index Index of child node
	 * @return \Aimeos\MShop\Catalog\Item\Iface Selected node
	 */
	public function getChild( int $index ) : \Aimeos\MShop\Common\Item\Tree\Iface;

	/**
	 * Returns all children of this node.
	 *
	 * @return array Numerically indexed list of nodes
	 */
	public function getChildren() : array;

	/**
	 * Returns the deleted children.
	 *
	 * @return \Aimeos\MShop\Catalog\Item\Iface[] List of removed children
	 */
	public function getChildrenDeleted() : array;

	/**
	 * Tests if a node has children.
	 *
	 * @return bool True if node has children, false if not
	 */
	public function hasChildren() : bool;

	/**
	 * Returns the node and its children as list
	 *
	 * @return \Aimeos\MShop\Catalog\Item\Iface[] Associative list of IDs as keys and nodes as values
	 */
	public function toList() : array;
}
