<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 */


/**
 * Database result set object for PDO connections.
 *
 * @package MW
 * @subpackage DB
 */
class MW_DB_Result_PDO extends MW_DB_Result_Abstract implements MW_DB_Result_Interface
{
	private $statement = null;
	private $style = array(
		MW_DB_Result_Abstract::FETCH_ASSOC => PDO::FETCH_ASSOC,
		MW_DB_Result_Abstract::FETCH_NUM => PDO::FETCH_NUM,
	);


	/**
	 * Initializes the result object.
	 *
	 * @param PDOStatement $stmt Statement object created by PDO
	 */
	public function __construct( PDOStatement $stmt )
	{
		$this->statement = $stmt;
	}


	/**
	 * Returns the number of rows affected by a INSERT, UPDATE or DELETE statement.
	 *
	 * @return integer Number of touched records
	 * @throws MW_DB_Exception if an error occured in the unterlying driver
	 */
	public function affectedRows()
	{
		try {
			return $this->statement->rowCount();
		} catch ( PDOException $e ) {
			throw new MW_DB_Exception( $e->getMessage(), $e->getCode(), $e->errorInfo );
		}
	}


	/**
	 * Retrieves the next row from database result set.
	 *
	 * @param integer $style The data can be returned as associative or numerical array
	 * @return Array Numeric or associative array of columns returned by the SQL statement
	 * @throws MW_DB_Exception if an error occured in the unterlying driver or the fetch style is unknown
	 */
	public function fetch( $style = MW_DB_Result_Abstract::FETCH_ASSOC )
	{
		try {
			return $this->statement->fetch( $this->style[$style] );
		} catch ( PDOException $e ) {
			throw new MW_DB_Exception( $e->getMessage(), $e->getCode(), $e->errorInfo );
		}
	}


	/**
	 * Cleans up pending database result sets.
	 *
	 * @throws MW_DB_Exception if an error occured in the unterlying driver
	 */
	public function finish()
	{
		try {
			$this->statement->closeCursor();
		} catch ( PDOException $e ) {
			throw new MW_DB_Exception( $e->getMessage(), $e->getCode(), $e->errorInfo );
		}
	}


	/**
	 * Retrieves the next database result set.
	 *
	 * @return boolean True if another result is available, false if not
	 * @throws MW_DB_Exception if an error occured in the unterlying driver
	 */
	public function nextResult()
	{
		try {
			return $this->statement->nextRowset();
		} catch ( PDOException $e ) {
			throw new MW_DB_Exception( $e->getMessage(), $e->getCode(), $e->errorInfo );
		}
	}
}
