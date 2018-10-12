<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018-2018
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Connection;


/**
 * Database connection class for DBAL connections
 *
 * @package MW
 * @subpackage DB
 */
class DBAL extends \Aimeos\MW\DB\Connection\Base implements \Aimeos\MW\DB\Connection\Iface
{
	private $connection;
	private $txnumber = 0;
	private $stmts = [];


	/**
	 * Initializes the DBAL connection object
	 *
	 * @param array $params Associative list of connection parameters
	 * @param string[] $stmts List of SQL statements to execute after connecting
	 */
	public function __construct( array $params, array $stmts )
	{
		parent::__construct( $params );

		$this->stmts = $stmts;
		$this->connect();
	}


	/**
	 * Connects (or reconnects) to the database server
	 *
	 * @return \Aimeos\MW\DB\Connection\Iface Connection instance for method chaining
	 */
	public function connect()
	{
		if( $this->connection && $this->connection->ping() ) {
			return $this;
		}

		$param = $this->getParameters();
		$param['driverOptions'] = [
			\PDO::ATTR_ORACLE_NULLS => \PDO::NULL_EMPTY_STRING,
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_TIMEOUT => 10,
		];

		$conn = $this->connection;

		$this->connection = \Doctrine\DBAL\DriverManager::getConnection( $param );
		$this->txnumber = 0;

		unset( $conn );

		foreach( $this->stmts as $stmt ) {
			$this->create( $stmt )->execute()->finish();
		}

		return $this;
	}


	/**
	 * Creates a DBAL database statement
	 *
	 * @param string $sql SQL statement, maybe with place holders
	 * @param integer $type Simple or prepared statement type constant from abstract class
	 * @return \Aimeos\MW\DB\Statement\Iface \PDO statement object
	 * @throws \Aimeos\MW\DB\Exception if type is invalid or the DBAL object throws an exception
	 */
	public function create( $sql, $type = \Aimeos\MW\DB\Connection\Base::TYPE_SIMPLE  )
	{
		try
		{
			switch( $type )
			{
				case \Aimeos\MW\DB\Connection\Base::TYPE_SIMPLE:
					return new \Aimeos\MW\DB\Statement\DBAL\Simple( $this, $sql );
				case \Aimeos\MW\DB\Connection\Base::TYPE_PREP:
					return new \Aimeos\MW\DB\Statement\DBAL\Prepared( $this, $sql );
				default:
					throw new \Aimeos\MW\DB\Exception( sprintf( 'Invalid value "%1$d" for statement type', $type ) );
			}
		}
		catch( \Exception $e)
		{
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode() );
		}
	}


	/**
	 * Returns the underlying connection object
	 *
	 * @return \Doctrine\DBAL\Connection Underlying connection object
	 */
	public function getRawObject()
	{
		return $this->connection;
	}


	/**
	 * Checks if a transaction is currently running
	 *
	 * @return boolean True if transaction is currently running, false if not
	 */
	public function inTransaction()
	{
		return $this->connection->getWrappedConnection()->inTransaction();
	}


	/**
	 * Starts a transaction for this connection.
	 *
	 * Transactions can't be nested and a new transaction can only be started
	 * if the previous transaction was committed or rolled back before.
	 */
	public function begin()
	{
		if( $this->txnumber === 0 )
		{
			if( $this->connection->getWrappedConnection()->beginTransaction() === false ) {
				throw new \Aimeos\MW\DB\Exception( 'Unable to start new transaction' );
			}
		}

		$this->txnumber++;
	}


	/**
	 * Commits the changes done inside of the transaction to the storage.
	 */
	public function commit()
	{
		if( $this->txnumber === 1 )
		{
			if( $this->connection->getWrappedConnection()->commit() === false ) {
				throw new \Aimeos\MW\DB\Exception( 'Failed to commit transaction' );
			}
		}

		$this->txnumber--;
	}


	/**
	 * Discards the changes done inside of the transaction.
	 */
	public function rollback()
	{
		if( $this->txnumber === 1 )
		{
			if( $this->connection->getWrappedConnection()->rollBack() === false ) {
				throw new \Aimeos\MW\DB\Exception( 'Failed to roll back transaction' );
			}
		}

		$this->txnumber--;
	}
}
