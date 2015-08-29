<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 */


/**
 * Dummy database connection class.
 *
 * @package MW
 * @subpackage DB
 */
class MW_DB_Connection_None
	extends MW_DB_Connection_Abstract
	implements MW_DB_Connection_Interface
{
	/**
	 * Creates a database statement.
	 *
	 * Throws an exception because there is no implementation available.
	 *
	 * @param string $sql SQL statement, maybe with place holders
	 * @param integer $type Simple or prepared statement type constant from abstract class
	 * @throws MW_DB_Exception
	 */
	public function create( $sql, $type = MW_DB_Connection_Abstract::TYPE_SIMPLE )
	{
		throw new MW_DB_Exception( 'This method is not implemented' );
	}


	/**
	 * Starts a transaction for this connection.
	 *
	 * Throws an exception because there is no implementation available.
	 *
	 * @throws MW_DB_Exception
	 */
	public function begin()
	{
		throw new MW_DB_Exception( 'This method is not implemented' );
	}


	/**
	 * Commits the changes done inside of the transaction to the storage.
	 *
	 * Throws an exception because there is no implementation available.
	 *
	 * @throws MW_DB_Exception
	 */
	public function commit()
	{
		throw new MW_DB_Exception( 'This method is not implemented' );
	}


	/**
	 * Discards the changes done inside of the transaction.
	 *
	 * Throws an exception because there is no implementation available.
	 *
	 * @throws MW_DB_Exception
	 */
	public function rollback()
	{
		throw new MW_DB_Exception( 'This method is not implemented' );
	}
}