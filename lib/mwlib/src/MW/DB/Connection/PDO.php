<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Connection;


/**
 * Database connection class for \PDO connections.
 *
 * @package MW
 * @subpackage DB
 */
class PDO extends \Aimeos\MW\DB\Connection\Base implements \Aimeos\MW\DB\Connection\Iface
{
	private $connection = null;
	private $txnumber = 0;


	/**
	 * Initializes the \PDO connection object.
	 *
	 * @param \PDO $connection \PDO connection object
	 */
	public function __construct( \PDO $connection )
	{
		$this->connection = $connection;
	}


	/**
	 * Creates a \PDO database statement.
	 *
	 * @param string $sql SQL statement, maybe with place holders
	 * @param integer $type Simple or prepared statement type constant from abstract class
	 * @return \Aimeos\MW\DB\Statement\Iface \PDO statement object
	 * @throws \Aimeos\MW\DB\Exception if type is invalid or the \PDO object throws an exception
	 */
	public function create( $sql, $type = \Aimeos\MW\DB\Connection\Base::TYPE_SIMPLE )
	{
		try {
			switch( $type )
			{
				case \Aimeos\MW\DB\Connection\Base::TYPE_SIMPLE:
					return new \Aimeos\MW\DB\Statement\PDO\Simple( $this->connection, $sql );
				case \Aimeos\MW\DB\Connection\Base::TYPE_PREP:
					return new \Aimeos\MW\DB\Statement\PDO\Prepared( $this->connection->prepare( $sql ) );
				default:
					throw new \Aimeos\MW\DB\Exception( sprintf( 'Invalid value "%1$d" for statement type', $type ) );
			}
		} catch( \PDOException $e ) {
			throw new \Aimeos\MW\DB\Exception($e->getMessage(), $e->getCode(), $e->errorInfo );
		}
	}


	/**
	 * Returns the underlying connection object
	 *
	 * @return \PDO Underlying connection object
	 */
	public function getRawObject()
	{
		return $this->connection;
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
				throw new \Aimeos\MW\DB\Exception( 'Unable to start new transaction' );
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
				throw new \Aimeos\MW\DB\Exception( 'Failed to commit transaction' );
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
					throw new \Aimeos\MW\DB\Exception( 'Failed to roll back transaction' );
				}
			}
		}
		catch( \PDOException $e )
		{
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode(), $e->errorInfo );
		}
	}
}
