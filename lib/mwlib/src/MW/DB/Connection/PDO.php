<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 */


/**
 * Database connection class for PDO connections.
 *
 * @package MW
 * @subpackage DB
 */
class MW_DB_Connection_PDO extends MW_DB_Connection_Abstract implements MW_DB_Connection_Interface
{
	private $connection = null;
	private $txnumber = 0;


	/**
	 * Initializes the PDO connection object.
	 *
	 * @param PDO $connection PDO connection object
	 */
	public function __construct( PDO $connection )
	{
		$this->connection = $connection;
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
					return new MW_DB_Statement_PDO_Simple($this->connection, $sql);
				case MW_DB_Connection_Abstract::TYPE_PREP:
					return new MW_DB_Statement_PDO_Prepared($this->connection->prepare($sql));
				default:
					throw new MW_DB_Exception( sprintf( 'Invalid value "%1$d" for statement type', $type ) );
			}
		} catch (PDOException $e) {
			throw new MW_DB_Exception($e->getMessage(), $e->getCode(), $e->errorInfo);
		}
	}


	/**
	 * Starts a transaction for this connection.
	 * Transactions can't be nested and a new transaction can only be started
	 * if the previous transaction was committed or rolled back before.
	 */
	public function begin()
	{
		if( $this->txnumber++ === 0 )
		{
			if( $this->connection->beginTransaction() === false ) {
				throw new MW_DB_Exception( 'Unable to start new transaction' );
			}
		}
	}


	/**
	 * Commits the changes done inside of the transaction to the storage.
	 */
	public function commit()
	{
		if( --$this->txnumber === 0 )
		{
			if( $this->connection->commit() === false ) {
				throw new MW_DB_Exception( 'Failed to commit transaction' );
			}
		}
	}


	/**
	 * Discards the changes done inside of the transaction.
	 */
	public function rollback()
	{
		try
		{
			if( --$this->txnumber === 0 )
			{
				if( $this->connection->rollBack() === false ) {
					throw new MW_DB_Exception( 'Failed to roll back transaction' );
				}
			}
		}
		catch( PDOException $e )
		{
			throw new MW_DB_Exception( $e->getMessage(), $e->getCode(), $e->errorInfo );
		}
	}
}
