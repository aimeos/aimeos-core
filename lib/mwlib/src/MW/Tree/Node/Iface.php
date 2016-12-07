<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Tree
 */


namespace Aimeos\MW\Tree\Node;


/**
 * Generic interface for all basic tree node implementations
 *
 * @package MW
 * @subpackage Tree
 */
interface Iface
{
	/**
	 * Returns the value associated with the given name.
	 *
	 * @param string $name Name of member variable tried to access
	 * @return mixed Value associated to the given name
	 * @throws \Aimeos\MW\Tree\Exception If no value is available for the given name
	 */
	public function __get( $name );

	/**
	 * Sets the new value associated with the given name.
	 *
	 * @param string $name Name of member variable tried to access
	 * @param mixed $value Value of member variable tried to access
	 * @return void
	 */
	public function __set( $name, $value );

	/**
	 * Tests if a value for the given name is available.
	 *
	 * @param string $name Name of member variable tried to access
	 * @return boolean True if a value is available, false if not
	 */
	public function __isset( $name );

	/**
	 * Removes the value associated to the given name.
	 *
	 * @param string $name Name of member variable tried to access
	 * @return void
	 */
	public function __unset( $name );

	/**
	 * Returns the unique ID of the node.
	 *
	 * @return string|null Unique ID of the node
	 */
	public function getId();

	/**
	 * Sets the unique ID of the node.
	 *
	 * @param mixed|null Unique ID of the node
	 * @return void
	 */
	public function setId( $id );

	/**
	 * Returns the name of the node.
	 *
	 * @return string Default name of the node
	 */
	public function getLabel();

	/**
	 * Sets the new name of the node.
	 *
	 * @param string $name New default name of the node
	 * @return void
	 */
	public function setLabel( $name );

	/**
	 * Returns the code of the node.
	 *
	 * @return string Code of the node
	 */
	public function getCode();

	/**
	 * Sets the new code of the node.
	 *
	 * @param string $name New code of the node
	 * @return void
	 */
	public function setCode( $name );


	/**
	 * Returns the status of the node.
	 *
	 * @return integer Greater than zero if enabled, zero or less than if not
	 */
	public function getStatus();

	/**
	 * Sets the new status of the node.
	 *
	 * @param integer $status Greater than zero if enabled, zero or less than if not
	 * @return void
	 */
	public function setStatus( $status );

	/**
	 * Returns a child of this node identified by its index.
	 *
	 * @param integer $index Index of child node
	 * @return \Aimeos\MW\Tree\Node\Iface Selected node
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
	 * @param \Aimeos\MW\Tree\Node\Iface $node Child node to add
	 * @return void
	 */
	public function addChild( \Aimeos\MW\Tree\Node\Iface $node );

	/**
	 * Returns the public values of the node as array.
	 *
	 * @return array Assciative list of key/value pairs
	 */
	public function toArray();

	/**
	 * Checks, whether this node was modified.
	 *
	 * @return boolean True if the content of the node is modified, false if not
	 */
	public function isModified();
}
