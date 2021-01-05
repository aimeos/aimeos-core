<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	 */
	public function __get( string $name );

	/**
	 * Sets the new value associated with the given name.
	 *
	 * @param string $name Name of member variable tried to access
	 * @param mixed $value Value of member variable tried to access
	 */
	public function __set( string $name, $value );

	/**
	 * Tests if a value for the given name is available.
	 *
	 * @param string $name Name of member variable tried to access
	 * @return bool True if a value is available, false if not
	 */
	public function __isset( string $name ) : bool;

	/**
	 * Removes the value associated to the given name.
	 *
	 * @param string $name Name of member variable tried to access
	 */
	public function __unset( string $name );

	/**
	 * Returns the unique ID of the node.
	 *
	 * @return string|null Unique ID of the node
	 */
	public function getId() : ?string;

	/**
	 * Sets the unique ID of the node.
	 *
	 * @param mixed|null $id Unique ID of the node
	 * @return \Aimeos\MW\Tree\Node\Iface Item object for method chaining
	 */
	public function setId( ?string $id ) : Iface;

	/**
	 * Returns the name of the node.
	 *
	 * @return string Default name of the node
	 */
	public function getLabel() : string;

	/**
	 * Sets the new name of the node.
	 *
	 * @param string $name New default name of the node
	 * @return \Aimeos\MW\Tree\Node\Iface Item object for method chaining
	 */
	public function setLabel( string $name ) : Iface;

	/**
	 * Returns the code of the node.
	 *
	 * @return string Code of the node
	 */
	public function getCode() : string;

	/**
	 * Sets the new code of the node.
	 *
	 * @param string $name New code of the node
	 * @return \Aimeos\MW\Tree\Node\Iface Item object for method chaining
	 */
	public function setCode( string $name ) : Iface;


	/**
	 * Returns the status of the node.
	 *
	 * @return int Greater than zero if enabled, zero or less than if not
	 */
	public function getStatus() : int;

	/**
	 * Sets the new status of the node.
	 *
	 * @param int $status Greater than zero if enabled, zero or less than if not
	 * @return \Aimeos\MW\Tree\Node\Iface Item object for method chaining
	 */
	public function setStatus( int $status ) : Iface;

	/**
	 * Returns a child of this node identified by its index.
	 *
	 * @param int $index Index of child node
	 * @return \Aimeos\MW\Tree\Node\Iface Selected node
	 */
	public function getChild( int $index ) : Iface;

	/**
	 * Returns all children of this node.
	 *
	 * @return \Aimeos\MW\Tree\Node\Iface[] Numerically indexed list of nodes
	 */
	public function getChildren() : array;

	/**
	 * Tests if a node has children.
	 *
	 * @return bool True if node has children, false if not
	 */
	public function hasChildren() : bool;

	/**
	 * Adds a child node to this node.
	 *
	 * @param \Aimeos\MW\Tree\Node\Iface $node Child node to add
	 * @return \Aimeos\MW\Tree\Node\Iface Item object for method chaining
	 */
	public function addChild( \Aimeos\MW\Tree\Node\Iface $node ) : Iface;

	/**
	 * Returns the public values of the node as array.
	 *
	 * @return array Assciative list of key/value pairs
	 */
	public function toArray() : array;

	/**
	 * Checks, whether this node was modified.
	 *
	 * @return bool True if the content of the node is modified, false if not
	 */
	public function isModified() : bool;
}
