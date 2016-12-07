<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Result;


/**
 * Database result set object for \PDO connections.
 *
 * @package MW
 * @subpackage DB
 */
class PDO extends \Aimeos\MW\DB\Result\Base implements \Aimeos\MW\DB\Result\Iface
{
	private $statement = null;
	private $style = array(
		\Aimeos\MW\DB\Result\Base::FETCH_ASSOC => \PDO::FETCH_ASSOC,
		\Aimeos\MW\DB\Result\Base::FETCH_NUM => \PDO::FETCH_NUM,
	);


	/**
	 * Initializes the result object.
	 *
	 * @param \PDOStatement $stmt Statement object created by \PDO
	 */
	public function __construct( \PDOStatement $stmt )
	{
		$this->statement = $stmt;
	}


	/**
	 * Returns the number of rows affected by a INSERT, UPDATE or DELETE statement.
	 *
	 * @return integer Number of touched records
	 * @throws \Aimeos\MW\DB\Exception if an error occured in the unterlying driver
	 */
	public function affectedRows()
	{
		try {
			return $this->statement->rowCount();
		} catch ( \PDOException $e ) {
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode(), $e->errorInfo );
		}
	}


	/**
	 * Retrieves the next row from database result set.
	 *
	 * @param integer $style The data can be returned as associative or numerical array
	 * @return Array Numeric or associative array of columns returned by the SQL statement
	 * @throws \Aimeos\MW\DB\Exception if an error occured in the unterlying driver or the fetch style is unknown
	 */
	public function fetch( $style = \Aimeos\MW\DB\Result\Base::FETCH_ASSOC )
	{
		try {
			return $this->statement->fetch( $this->style[$style] );
		} catch ( \PDOException $e ) {
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode(), $e->errorInfo );
		}
	}


	/**
	 * Cleans up pending database result sets.
	 *
	 * @throws \Aimeos\MW\DB\Exception if an error occured in the unterlying driver
	 */
	public function finish()
	{
		try {
			$this->statement->closeCursor();
		} catch ( \PDOException $e ) {
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode(), $e->errorInfo );
		}
	}


	/**
	 * Retrieves the next database result set.
	 *
	 * @return boolean True if another result is available, false if not
	 */
	public function nextResult()
	{
		try {
			return $this->statement->nextRowset();
		} catch ( \PDOException $e ) {
			return false;
		}
	}
}
