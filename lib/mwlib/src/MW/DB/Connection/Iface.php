<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Connection;


/**
 * Required functions for database connection objects.
 *
 * @package MW
 * @subpackage DB
 */
interface Iface
{
	/**
	 * Connects (or reconnects) to the database server
	 *
	 * @return \Aimeos\MW\DB\Connection\Iface Connection instance for method chaining
	 */
	public function connect() : Iface;


	/**
	 * Creates a database statement.
	 *
	 * @param string $sql SQL statement, maybe with place holders
	 * @return \Aimeos\MW\DB\Statement\Iface
	 */
	public function create( string $sql ) : \Aimeos\MW\DB\Statement\Iface;


	/**
	 * Escapes the value if necessary for direct inclusion in SQL statement.
	 *
	 * @param string|null $data Value to escape or null for no value
	 * @return string Escaped string
	 */
	public function escape( string $data = null ) : string;


	/**
	 * Returns the underlying connection object
	 *
	 * @return mixed Underlying connection object
	 */
	public function getRawObject();


	/**
	 * Checks if a transaction is currently running
	 *
	 * @return bool True if transaction is currently running, false if not
	 */
	public function inTransaction() : bool;


	/**
	 * Starts a transaction for this connection.
	 *
	 * Transactions can't be nested and a new transaction can only be started
	 * if the previous transaction was committed or rolled back before.
	 *
	 * @return \Aimeos\MW\DB\Connection\Iface Connection instance for method chaining
	 */
	public function begin() : Iface;


	/**
	 * Commits the changes done inside of the transaction to the storage.
	 *
	 * @return \Aimeos\MW\DB\Connection\Iface Connection instance for method chaining
	 */
	public function commit() : Iface;


	/**
	 * Discards the changes done inside of the transaction.
	 *
	 * @return \Aimeos\MW\DB\Connection\Iface Connection instance for method chaining
	 */
	public function rollback() : Iface;
}
