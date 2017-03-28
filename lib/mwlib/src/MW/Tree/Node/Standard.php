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
 * Default implementation of a basic tree node
 *
 * @package MW
 * @subpackage Tree
 */
class Standard extends \Aimeos\MW\Common\Item\Base implements \Aimeos\MW\Tree\Node\Iface, \Countable
{
	private $values;
	private $children = [];
	private $modified = false;


	/**
	 * Initializes the instance with the given values.
	 *
	 * @param array $values Node values for internal use
	 * @param array $children Children of the node implementing \Aimeos\MW\Tree\Node\Iface
	 * @throws \Aimeos\MW\Common\Exception if the children doesn't implement the interface
	 */
	public function __construct( array $values = [], $children = [] )
	{
		\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MW\\Tree\\Node\\Iface', $children );

		$this->values = $values;
		$this->children = $children;

		$this->modified = false;
	}


	/**
	 * Returns the value associated with the given name.
	 *
	 * @param string $name Name of member variable tried to access
	 * @return mixed Value associated to the given name
	 * @throws \Aimeos\MW\Tree\Exception If no value is available for the given name
	 */
	public function __get( $name )
	{
		if( in_array( $name, array_keys( $this->values ) ) ) {
			return $this->values[$name];
		}

		throw new \Aimeos\MW\Tree\Exception( sprintf( 'No value for "%1$s" set in node', $name ) );
	}


	/**
	 * Sets the new value associated with the given name.
	 *
	 * @param string $name Name of member variable tried to access
	 * @param mixed $value Value of member variable tried to access
	 */
	public function __set( $name, $value )
	{
		$this->values[$name] = $value;
		$this->modified = true;
	}


	/**
	 * Tests if a value for the given name is available.
	 *
	 * @param string $name Name of member variable tried to access
	 * @return boolean True if a value is available, false if not
	 */
	public function __isset( $name )
	{
		return array_key_exists( $name, $this->values );
	}


	/**
	 * Removes the value associated to the given name.
	 *
	 * @param string $name Name of member variable tried to access
	 */
	public function __unset( $name )
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
	public function getId()
	{
		return ( isset( $this->values['id'] ) ? (string) $this->values['id'] : null );
	}


	/**
	 * Sets the unique ID of the node.
	 *
	 * @param mixed|null Unique ID of the node
	 */
	public function setId( $id )
	{
		if ( $id === null ) {
			$this->modified = true;
		}

		$this->values['id'] = $id;
	}


	/**
	 * Returns the name of the node.
	 *
	 * @return string Default name of the node
	 */
	public function getLabel()
	{
		return ( isset( $this->values['label'] ) ? (string) $this->values['label'] : '' );
	}


	/**
	 * Sets the new name of the node.
	 *
	 * @param string $name New default name of the node
	 */
	public function setLabel( $name )
	{
		if ( $name == $this->getLabel() ) { return; }

		$this->values['label'] = (string) $name;
		$this->modified = true;
	}


	/**
	 * Returns the Code of the node.
	 *
	 * @return string Code of the node
	 */
	public function getCode()
	{
		return ( isset( $this->values['code'] ) ? (string) $this->values['code'] : '' );
	}


	/**
	 * Sets the new code of the node.
	 *
	 * @param string $name New code of the node
	 */
	public function setCode( $name )
	{
		if ( $name == $this->getCode() ) { return; }

		$this->values['code'] = (string) $name;
		$this->modified = true;
	}

	/**
	 * Returns the status of the node.
	 *
	 * @return integer Greater than zero if enabled, zero or less than if not
	 */
	public function getStatus()
	{
		return ( isset( $this->values['status'] ) ? (int) $this->values['status'] : 0 );
	}


	/**
	 * Sets the new status of the node.
	 *
	 * @param integer Greater than zero if enabled, zero or less than if not
	 */
	public function setStatus( $status )
	{
		if ( $status == $this->getStatus() ) { return; }

		$this->values['status'] = (int) $status;
		$this->modified = true;
	}


	/**
	 * Returns a child of this node identified by its index.
	 *
	 * @param integer $index Index of child node
	 * @return \Aimeos\MW\Tree\Node\Iface Selected node
	 * @throws \Aimeos\MW\Tree\Exception If there's no child at the given position
	 */
	public function getChild($index)
	{
		if( isset( $this->children[$index] ) ) {
			return $this->children[$index];
		}

		throw new \Aimeos\MW\Tree\Exception( 'Invalid index for child' );
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
		return ( count( $this->children ) > 0 ? true : false );
	}


	/**
	 * Adds a child node to this node.
	 *
	 * @param \Aimeos\MW\Tree\Node\Iface $node Child node to add
	 */
	public function addChild( \Aimeos\MW\Tree\Node\Iface $node )
	{
		// don't set the modified flag as it's only for the values
		$this->children[] = $node;
	}

	/**
	 * Returns the public values of the node as array.
	 *
	 * @return array Assciative list of key/value pairs
	 */
	public function toArray()
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
	 * @return boolean True if the content of the node is modified, false if not
	 */
	public function isModified()
	{
		return $this->modified;
	}


	/**
	 * Counts children
	 *
	 * @return integer Count of this nodes children
	 */
	public function count()
	{
		return count($this->children);
	}
}
