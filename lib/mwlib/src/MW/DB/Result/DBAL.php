<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Result;


/**
 * Database result set object for DBAL connections
 *
 * @package MW
 * @subpackage DB
 */
class DBAL extends \Aimeos\MW\DB\Result\Base implements \Aimeos\MW\DB\Result\Iface
{
	private $statement = null;
	private $style = array(
		\Aimeos\MW\DB\Result\Base::FETCH_ASSOC => \PDO::FETCH_ASSOC,
		\Aimeos\MW\DB\Result\Base::FETCH_NUM => \PDO::FETCH_NUM,
	);


	/**
	 * Initializes the result object
	 *
	 * @param \Doctrine\DBAL\Driver\Statement $stmt Statement object created by DBAL
	 */
	public function __construct( \Doctrine\DBAL\Driver\Statement $stmt )
	{
		$this->statement = $stmt;
	}


	/**
	 * Returns the number of rows affected by a INSERT, UPDATE or DELETE statement
	 *
	 * @return integer Number of touched records
	 * @throws \Aimeos\MW\DB\Exception if an error occured in the unterlying driver
	 */
	public function affectedRows()
	{
		try {
			return $this->statement->rowCount();
		} catch ( \Doctrine\DBAL\DBALException $e ) {
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode(), join( ', ', $this->statement->errorInfo() ) );
		}
	}


	/**
	 * Retrieves the next row from database result set
	 *
	 * @param integer $style The data can be returned as associative or numerical array
	 * @return Array Numeric or associative array of columns returned by the SQL statement
	 * @throws \Aimeos\MW\DB\Exception if an error occured in the unterlying driver or the fetch style is unknown
	 */
	public function fetch( $style = \Aimeos\MW\DB\Result\Base::FETCH_ASSOC )
	{
		try {
			return $this->statement->fetch( $this->style[$style] );
		} catch ( \Doctrine\DBAL\DBALException $e ) {
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode(), join( ', ', $this->statement->errorInfo() ) );
		}
	}


	/**
	 * Cleans up pending database result sets
	 *
	 * @throws \Aimeos\MW\DB\Exception if an error occured in the unterlying driver
	 */
	public function finish()
	{
		try {
			$this->statement->closeCursor();
		} catch ( \Doctrine\DBAL\DBALException $e ) {
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode(), join( ', ', $this->statement->errorInfo() ) );
		}
	}


	/**
	 * Retrieves the next database result set
	 *
	 * @return boolean True if another result is available, false if not
	 * @throws \Aimeos\MW\DB\Exception if an error occured in the unterlying driver
	 */
	public function nextResult()
	{
		return false;
	}
}
