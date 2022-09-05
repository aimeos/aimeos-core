<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Iterator;


/**
 * Default implementation for manager iterators
 *
 * @package MShop
 * @subpackage Common
 */
class DB implements Iface
{
	private $conn;
	private $current;
	private $result;


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\Base\DB\Connection\Iface $conn Database connection
	 * @param \Aimeos\Base\DB\Result\Iface $result Result set to iterate over
	 */
	public function __construct( \Aimeos\Base\DB\Connection\Iface $conn, \Aimeos\Base\DB\Result\Iface $result )
	{
		$this->current = $result->fetch();
		$this->result = $result;
		$this->conn = $conn;
	}


	/**
	 * Terminates the iterator
	 */
	public function close() : void
	{
		$this->current = null;
		$this->result->finish();
		$this->conn->close();
	}


	/**
	 * Returns the current element
	 *
	 * @return array Associative list of key/value pairs of the current record
	 */
	#[\ReturnTypeWillChange]
	public function current()
	{
		return $this->current;
	}


	/**
	 * Returns the key of the current element
	 *
	 * @return string ID of the current record
	 */
	#[\ReturnTypeWillChange]
	public function key()
	{
		return is_array( $this->current ) ? current( $this->current ) : null;
	}


	/**
	 * Moves forward to next element
	 */
	public function next() : void
	{
		$this->current = $this->result->fetch();
	}


	/**
	 * Rewinds the Iterator to the first element
	 */
	public function rewind() : void
	{
	}


	/**
	 * Checks if current position is valid
	 *
	 * @return bool TRUE if more records are available, FALSE if not
	 */
	public function valid() : bool
	{
		return is_array( $this->current );
	}
}
