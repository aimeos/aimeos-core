<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 * @version $Id: Interface.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Required functions for database connection objects.
 *
 * @package MW
 * @subpackage DB
 */
interface MW_DB_Connection_Interface
{
	/**
	 * Creates a database statement.
	 *
	 * @param string $sql SQL statement, maybe with place holders
	 * @param integer $type Simple or prepared statement type
	 * @return MW_DB_Statement_Interface
	 */
	public function create( $sql, $type = MW_DB_Connection_Abstract::TYPE_SIMPLE );


	/**
	 * Escapes the value if necessary for direct inclusion in SQL statement.
	 *
	 * @param string $data Value to escape
	 * @return string Escaped string
	 */
	public function escape( $data );


	/**
	 * Starts a transaction for this connection.
	 * Transactions can't be nested and a new transaction can only be started
	 * if the previous transaction was committed or rolled back before.
	 */
	public function begin();


	/**
	 * Commits the changes done inside of the transaction to the storage.
	 */
	public function commit();


	/**
	 * Discards the changes done inside of the transaction.
	 */
	public function rollback();
}
