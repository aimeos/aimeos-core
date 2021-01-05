<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Result;


/**
 * Database result set object for PDO connections.
 *
 * @package MW
 * @subpackage DB
 */
class PDO extends \Aimeos\MW\DB\Result\Base implements \Aimeos\MW\DB\Result\Iface
{
	private $statement;


	/**
	 * Initializes the result object.
	 *
	 * @param \PDOStatement $stmt Statement object created by PDO
	 */
	public function __construct( \PDOStatement $stmt )
	{
		$this->statement = $stmt;
	}


	/**
	 * Clears the result set if anything is left.
	 */
	public function __destruct()
	{
		$this->statement->closeCursor();
	}


	/**
	 * Returns the number of rows affected by a INSERT, UPDATE or DELETE statement.
	 *
	 * @return int Number of touched records
	 * @throws \Aimeos\MW\DB\Exception if an error occured in the unterlying driver
	 */
	public function affectedRows() : int
	{
		try {
			return $this->statement->rowCount();
		} catch( \PDOException $e ) {
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode(), $e->errorInfo );
		}
	}


	/**
	 * Retrieves the next row from database result set.
	 *
	 * @param int $style The data can be returned as associative or numerical array
	 * @return array|null Numeric or associative array of columns returned by the database or null if no more rows are available
	 * @throws \Aimeos\MW\DB\Exception if an error occured in the unterlying driver or the fetch style is unknown
	 */
	public function fetch( int $style = \Aimeos\MW\DB\Result\Base::FETCH_ASSOC ) : ?array
	{
		try {
			return $this->statement->fetch( $style ? \PDO::FETCH_ASSOC : \PDO::FETCH_NUM ) ?: null;
		} catch( \PDOException $e ) {
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode(), $e->errorInfo );
		}
	}


	/**
	 * Cleans up pending database result sets.
	 *
	 * @return \Aimeos\MW\DB\Result\Iface Result instance for method chaining
	 * @throws \Aimeos\MW\DB\Exception if an error occured in the unterlying driver
	 */
	public function finish() : Iface
	{
		try {
			$this->statement->closeCursor();
		} catch( \PDOException $e ) {
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode(), $e->errorInfo );
		}

		return $this;
	}


	/**
	 * Retrieves the next database result set.
	 *
	 * @return bool True if another result is available, false if not
	 */
	public function nextResult() : bool
	{
		try {
			return $this->statement->nextRowset();
		} catch( \PDOException $e ) {
			return false;
		}
	}
}
