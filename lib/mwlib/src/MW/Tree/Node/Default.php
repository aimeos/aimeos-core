<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Tree
 */


/**
 * Default implementation of a basic tree node
 *
 * @package MW
 * @subpackage Tree
 */
class MW_Tree_Node_Default extends MW_Common_Item_Abstract implements MW_Tree_Node_Interface, Countable
{
	private $_values;
	private $_children = array();
	private $_modified = false;


	/**
	 * Initializes the instance with the given values.
	 *
	 * @param array $values Node values for internal use
	 * @param array $children Children of the node implementing MW_Tree_Node_Interface
	 * @throws MW_Common_Exception if the children doesn't implement the interface
	 */
	public function __construct( array $values = array(), $children = array() )
	{
		MW_Common_Abstract::checkClassList( 'MW_Tree_Node_Interface', $children );

		$this->_values = $values;
		$this->_children = $children;

		$this->_modified = false;
	}


	/**
	 * Returns the value associated with the given name.
	 *
	 * @param string $name Name of member variable tried to access
	 * @return mixed Value associated to the given name
	 * @throws MW_Tree_Exception If no value is available for the given name
	 */
	public function __get( $name )
	{
		if( in_array( $name, array_keys( $this->_values ) ) ) {
			return $this->_values[$name];
		}

		throw new MW_Tree_Exception( sprintf( 'No value for "%1$s" set in node', $name ) );
	}


	/**
	 * Sets the new value associated with the given name.
	 *
	 * @param string $name Name of member variable tried to access
	 * @param mixed $value Value of member variable tried to access
	 */
	public function __set( $name, $value )
	{
		$this->_values[$name] = $value;
		$this->_modified = true;
	}


	/**
	 * Tests if a value for the given name is available.
	 *
	 * @param string $name Name of member variable tried to access
	 * @return boolean True if a value is available, false if not
	 */
	public function __isset( $name )
	{
		return array_key_exists( $name, $this->_values );
	}


	/**
	 * Removes the value associated to the given name.
	 *
	 * @param string $name Name of member variable tried to access
	 */
	public function __unset( $name )
	{
		if( array_key_exists( $name, $this->_values ) )
		{
			unset( $this->_values[$name] );
			$this->_modified = true;
		}
	}


	/**
	 * Returns the unique ID of the node.
	 *
	 * @return string|null Unique ID of th node
	 */
	public function getId()
	{
		return ( isset( $this->_values['id'] ) ? (string) $this->_values['id'] : null );
	}


	/**
	 * Sets the unique ID of the node.
	 *
	 * @param mixed|null Unique ID of the node
	 */
	public function setId( $id )
	{
		if ( $id === null ) {
			$this->_modified = true;
		}

		$this->_values['id'] = $id;
	}


	/**
	 * Returns the name of the node.
	 *
	 * @return string Default name of the node
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the new name of the node.
	 *
	 * @param string $name New default name of the node
	 */
	public function setLabel( $name )
	{
		if ( $name == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $name;
		$this->_modified = true;
	}


	/**
	 * Returns the Code of the node.
	 *
	 * @return string Code of the node
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets the new code of the node.
	 *
	 * @param string $name New code of the node
	 */
	public function setCode( $name )
	{
		if ( $name == $this->getCode() ) { return; }

		$this->_values['code'] = (string) $name;
		$this->_modified = true;
	}

	/**
	 * Returns the status of the node.
	 *
	 * @return integer Greater than zero if enabled, zero or less than if not
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the new status of the node.
	 *
	 * @param integer Greater than zero if enabled, zero or less than if not
	 */
	public function setStatus( $status )
	{
		if ( $status == $this->getStatus() ) { return; }

		$this->_values['status'] = (int) $status;
		$this->_modified = true;
	}


	/**
	 * Returns a child of this node identified by its index.
	 *
	 * @param integer $index Index of child node
	 * @return MW_Tree_Node_Interface Selected node
	 * @throws MW_Tree_Exception If there's no child at the given position
	 */
	public function getChild($index)
	{
		if( isset( $this->_children[$index] ) ) {
			return $this->_children[$index];
		}

		throw new MW_Tree_Exception( 'Invalid index for child' );
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
		return ( count( $this->_children ) > 0 ? true : false );
	}


	/**
	 * Adds a child node to this node.
	 *
	 * @param MW_Tree_Node_Interface $node Child node to add
	 */
	public function addChild( MW_Tree_Node_Interface $node )
	{
		// don't set the modified flag as it's only for the values
		$this->_children[] = $node;
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
		return $this->_modified;
	}


	/**
	 * Counts children
	 *
	 * @return integer Count of this nodes children
	 */
	public function count()
	{
		return count($this->_children);
	}
}
