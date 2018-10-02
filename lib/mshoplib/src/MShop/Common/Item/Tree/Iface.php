<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Tree;


/**
 * Common interface for tree items
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface extends \Aimeos\MShop\Common\Item\Status\Iface
{
	/**
	 * Returns the code of the item.
	 *
	 * @return string Code of the item
	 */
	public function getCode();

	/**
	 * Sets the code of the item.
	 *
	 * @param string $name New code of the item
	 * @return \Aimeos\MShop\Common\Item\Tree\Iface Item for chaining method calls
	 */
	public function setCode( $name );

	/**
	 * Returns the name of the item.
	 *
	 * @return string Name of the item
	 */
	public function getLabel();

	/**
	 * Sets the new name of the item.
	 *
	 * @param string $name New name of the item
	 * @return \Aimeos\MShop\Common\Item\Tree\Iface Item for chaining method calls
	 */
	public function setLabel( $name );

	/**
	 * Returns the level of the item in the tree
	 *
	 * @return integer Level of the item starting with "0" for the root node
	 */
	public function getLevel();

	/**
	 * Returns the ID of the parent category
	 *
	 * @return string Unique ID of the parent category
	 */
	public function getParentId();

	/**
	 * Returns a child of this node identified by its index.
	 *
	 * @param integer $index Index of child node
	 * @return \Aimeos\MShop\Common\Item\Tree\Iface Selected node
	 */
	public function getChild( $index );

	/**
	 * Returns all children of this node.
	 *
	 * @return array Numerically indexed list of nodes
	 */
	public function getChildren();

	/**
	 * Tests if a node has children.
	 *
	 * @return boolean True if node has children, false if not
	 */
	public function hasChildren();

	/**
	 * Adds a child node to this node.
	 *
	 * @param \Aimeos\MShop\Common\Item\Tree\Iface $item Child node to add
	 * @return \Aimeos\MShop\Common\Item\Tree\Iface Catalog item for chaining method calls
	 */
	public function addChild( \Aimeos\MShop\Common\Item\Tree\Iface $item );
}
