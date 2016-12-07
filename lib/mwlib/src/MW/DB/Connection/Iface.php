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
 * Required functions for database connection objects.
 *
 * @package MW
 * @subpackage DB
 */
interface Iface
{
	/**
	 * Creates a database statement.
	 *
	 * @param string $sql SQL statement, maybe with place holders
	 * @param integer $type Simple or prepared statement type
	 * @return \Aimeos\MW\DB\Statement\Iface
	 */
	public function create( $sql, $type = \Aimeos\MW\DB\Connection\Base::TYPE_SIMPLE );


	/**
	 * Escapes the value if necessary for direct inclusion in SQL statement.
	 *
	 * @param string $data Value to escape
	 * @return string Escaped string
	 */
	public function escape( $data );


	/**
	 * Returns the underlying connection object
	 *
	 * @return mixed Underlying connection object
	 */
	public function getRawObject();


	/**
	 * Starts a transaction for this connection.
	 * Transactions can't be nested and a new transaction can only be started
	 * if the previous transaction was committed or rolled back before.
	 * @return void
	 */
	public function begin();


	/**
	 * Commits the changes done inside of the transaction to the storage.
	 * @return void
	 */
	public function commit();


	/**
	 * Discards the changes done inside of the transaction.
	 * @return void
	 */
	public function rollback();
}
