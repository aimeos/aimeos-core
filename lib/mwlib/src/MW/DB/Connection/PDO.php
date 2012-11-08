<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 * @version $Id: PDO.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Database connection class for PDO connections.
 *
 * @package MW
 * @subpackage DB
 */
class  MW_DB_Connection_PDO extends MW_DB_Connection_Abstract implements MW_DB_Connection_Interface
{
	private $_connection = null;


	/**
	 * Initializes the PDO connection object.
	 *
	 * @param PDO $connection PDO connection object
	 */
	public function __construct( PDO $connection )
	{
		$this->_connection = $connection;
	}


	/**
	 * Creates a PDO database statement.
	 *
	 * @param string $sql SQL statement, maybe with place holders
	 * @param integer $type Simple or prepared statement type constant from abstract class
	 * @return MW_DB_Statement_Interface PDO statement object
	 * @throws MW_DB_Exception if type is invalid or the PDO object throws an exception
	 */
	public function create($sql, $type = MW_DB_Connection_Abstract::TYPE_SIMPLE)
	{
		try {
			switch ($type)
			{
				case MW_DB_Connection_Abstract::TYPE_SIMPLE:
					return new MW_DB_Statement_PDO_Simple($this->_connection, $sql);
				case MW_DB_Connection_Abstract::TYPE_PREP:
					return new MW_DB_Statement_PDO_Prepared($this->_connection->prepare($sql));
				default:
					throw new MW_DB_Exception( sprintf( 'Invalid value "%1$d" for statement type', $type ) );
			}
		} catch (PDOException $e) {
			throw new MW_DB_Exception($e->getMessage(), $e->getCode(), $e->errorInfo);
		}
	}


	/**
	 * Escapes the value if necessary for direct inclusion in SQL statement.
	 *
	 * @param string $data Value to escape
	 * @return string Escaped string
	 */
	public function escape($data)
	{
		return str_replace( '\'', '\'\'', $data);
	}


	/**
	 * Starts a transaction for this connection.
	 * Transactions can't be nested and a new transaction can only be started
	 * if the previous transaction was committed or rolled back before.
	 */
	public function begin()
	{
		if( $this->_connection->beginTransaction() === false ) {
			throw new MW_DB_Exception( 'Unable to start new transaction' );
		}
	}


	/**
	 * Commits the changes done inside of the transaction to the storage.
	 */
	public function commit()
	{
		if( $this->_connection->commit() === false ) {
			throw new MW_DB_Exception( 'Failed to commit transaction' );
		}
	}


	/**
	 * Discards the changes done inside of the transaction.
	 */
	public function rollback()
	{
		try {
			if( $this->_connection->rollBack() === false ) {
				throw new MW_DB_Exception( 'Failed to roll back transaction' );
			}
		} catch( PDOException $e ) {
			throw new MW_DB_Exception( $e->getMessage(), $e->getCode(), $e->errorInfo );
		}
	}
}
