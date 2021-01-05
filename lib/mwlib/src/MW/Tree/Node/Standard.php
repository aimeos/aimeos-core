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
 * Default implementation of a basic tree node
 *
 * @package MW
 * @subpackage Tree
 */
class Standard extends \Aimeos\MW\Common\Item\Base implements \Aimeos\MW\Tree\Node\Iface, \Countable
{
	private $values;
	private $children;
	private $modified = false;


	/**
	 * Initializes the instance with the given values.
	 *
	 * @param array $values Node values for internal use
	 * @param \Aimeos\MW\Tree\Node\Iface[] $children Children of the node
	 * @throws \Aimeos\MW\Common\Exception if the children doesn't implement the interface
	 */
	public function __construct( array $values = [], array $children = [] )
	{
		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MW\Tree\Node\Iface::class, $children );

		$this->values = $values;
		$this->children = $children;
	}


	/**
	 * Returns the value associated with the given name.
	 *
	 * @param string $name Name of member variable tried to access
	 * @return mixed Value associated to the given name or NULL if not available
	 */
	public function __get( string $name )
	{
		return $this->values[$name] ?? null;
	}


	/**
	 * Sets the new value associated with the given name.
	 *
	 * @param string $name Name of member variable tried to access
	 * @param mixed $value Value of member variable tried to access
	 */
	public function __set( string $name, $value )
	{
		if( !array_key_exists( $name, $this->values ) || $this->values[$name] !== $value )
		{
			$this->values[$name] = $value;
			$this->modified = true;
		}
	}


	/**
	 * Tests if a value for the given name is available.
	 *
	 * @param string $name Name of member variable tried to access
	 * @return bool True if a value is available, false if not
	 */
	public function __isset( string $name ) : bool
	{
		return array_key_exists( $name, $this->values );
	}


	/**
	 * Removes the value associated to the given name.
	 *
	 * @param string $name Name of member variable tried to access
	 */
	public function __unset( string $name )
	{
		if( array_key_exists( $name, $this->values ) )
		{
			unset( $this->values[$name] );
			$this->modified = true;
		}
	}


	/**
	 * Returns the unique ID of the node.
	 *
	 * @return string|null Unique ID of th node
	 */
	public function getId() : ?string
	{
		return ( isset( $this->values['id'] ) ? (string) $this->values['id'] : null );
	}


	/**
	 * Sets the unique ID of the node.
	 *
	 * @param mixed|null $id Unique ID of the node
	 * @return \Aimeos\MW\Tree\Node\Iface Item object for method chaining
	 */
	public function setId( ?string $id ) : Iface
	{
		$this->values['id'] = $id;
		$this->modified = ( $id === null ? true : false );

		return $this;
	}


	/**
	 * Returns the name of the node.
	 *
	 * @return string Default name of the node
	 */
	public function getLabel() : string
	{
		return ( isset( $this->values['label'] ) ? (string) $this->values['label'] : '' );
	}


	/**
	 * Sets the new name of the node.
	 *
	 * @param string $name New default name of the node
	 * @return \Aimeos\MW\Tree\Node\Iface Item object for method chaining
	 */
	public function setLabel( string $name ) : Iface
	{
		if( (string) $name !== $this->getLabel() )
		{
			$this->values['label'] = (string) $name;
			$this->modified = true;
		}

		return $this;
	}


	/**
	 * Returns the Code of the node.
	 *
	 * @return string Code of the node
	 */
	public function getCode() : string
	{
		return ( isset( $this->values['code'] ) ? (string) $this->values['code'] : '' );
	}


	/**
	 * Sets the new code of the node.
	 *
	 * @param string $name New code of the node
	 * @return \Aimeos\MW\Tree\Node\Iface Item object for method chaining
	 */
	public function setCode( string $name ) : Iface
	{
		if( (string) $name !== $this->getCode() )
		{
			$this->values['code'] = (string) $name;
			$this->modified = true;
		}

		return $this;
	}

	/**
	 * Returns the status of the node.
	 *
	 * @return int Greater than zero if enabled, zero or less than if not
	 */
	public function getStatus() : int
	{
		return ( isset( $this->values['status'] ) ? (int) $this->values['status'] : 1 );
	}


	/**
	 * Sets the new status of the node.
	 *
	 * @param int $status Greater than zero if enabled, zero or less than if not
	 * @return \Aimeos\MW\Tree\Node\Iface Item object for method chaining
	 */
	public function setStatus( int $status ) : Iface
	{
		if( (int) $status !== $this->getStatus() )
		{
			$this->values['status'] = (int) $status;
			$this->modified = true;
		}

		return $this;
	}


	/**
	 * Returns a child of this node identified by its index.
	 *
	 * @param int $index Index of child node
	 * @return \Aimeos\MW\Tree\Node\Iface Selected node
	 * @throws \Aimeos\MW\Tree\Exception If there's no child at the given position
	 */
	public function getChild( int $index ) : Iface
	{
		if( isset( $this->children[$index] ) ) {
			return $this->children[$index];
		}

		throw new \Aimeos\MW\Tree\Exception( 'Invalid index for child' );
	}


	/**
	 * Returns all children of this node.
	 *
	 * @return \Aimeos\MW\Tree\Node\Iface[] Numerically indexed list of nodes
	 */
	public function getChildren() : array
	{
		return $this->children;
	}


	/**
	 * Tests if a node has children.
	 *
	 * @return bool True if node has children, false if not
	 */
	public function hasChildren() : bool
	{
		return ( count( $this->children ) > 0 ? true : false );
	}


	/**
	 * Adds a child node to this node.
	 *
	 * @param \Aimeos\MW\Tree\Node\Iface $node Child node to add
	 * @return \Aimeos\MW\Tree\Node\Iface Item object for method chaining
	 */
	public function addChild( \Aimeos\MW\Tree\Node\Iface $node ) : Iface
	{
		// don't set the modified flag as it's only for the values
		$this->children[] = $node;
		return $this;
	}

	/**
	 * Returns the public values of the node as array.
	 *
	 * @return array Assciative list of key/value pairs
	 */
	public function toArray() : array
	{
		return array(
			'id' => $this->getId(),
			'label' => $this->getLabel(),
			'status' => $this->getStatus(),
		);
	}


	/**
	 * Checks, whether this node was modified.
	 *
	 * @return bool True if the content of the node is modified, false if not
	 */
	public function isModified() : bool
	{
		return $this->modified;
	}


	/**
	 * Counts children
	 *
	 * @return int Count of this nodes children
	 */
	public function count() : int
	{
		return count( $this->children );
	}
}
