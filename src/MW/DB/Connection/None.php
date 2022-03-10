<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2022
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\Base\DB\Connection;


/**
 * Dummy database connection class.
 *
 * @package MW
 * @subpackage DB
 */
class None
	extends \Aimeos\Base\DB\Connection\Base
	implements \Aimeos\Base\DB\Connection\Iface
{
	/**
	 * Connects (or reconnects) to the database server
	 *
	 * @return \Aimeos\Base\DB\Connection\Iface Connection instance for method chaining
	 */
	public function connect() : Iface
	{
		throw new \Aimeos\Base\DB\Exception( 'This method is not implemented' );
	}


	/**
	 * Creates a database statement.
	 *
	 * Throws an exception because there is no implementation available.
	 *
	 * @param string $sql SQL statement, maybe with place holders
	 * @throws \Aimeos\Base\DB\Exception
	 */
	public function create( string $sql ) : \Aimeos\Base\DB\Statement\Iface
	{
		throw new \Aimeos\Base\DB\Exception( 'This method is not implemented' );
	}


	/**
	 * Returns the underlying connection object
	 *
	 * Throws an exception because there is no implementation available.
	 *
	 * @throws \Aimeos\Base\DB\Exception
	 */
	public function getRawObject()
	{
		throw new \Aimeos\Base\DB\Exception( 'This method is not implemented' );
	}


	/**
	 * Starts a transaction for this connection.
	 *
	 * Throws an exception because there is no implementation available.
	 *
	 * @throws \Aimeos\Base\DB\Exception
	 */
	public function begin() : Iface
	{
		throw new \Aimeos\Base\DB\Exception( 'This method is not implemented' );
	}


	/**
	 * Commits the changes done inside of the transaction to the storage.
	 *
	 * Throws an exception because there is no implementation available.
	 *
	 * @throws \Aimeos\Base\DB\Exception
	 */
	public function commit() : Iface
	{
		throw new \Aimeos\Base\DB\Exception( 'This method is not implemented' );
	}


	/**
	 * Discards the changes done inside of the transaction.
	 *
	 * Throws an exception because there is no implementation available.
	 *
	 * @throws \Aimeos\Base\DB\Exception
	 */
	public function rollback() : Iface
	{
		throw new \Aimeos\Base\DB\Exception( 'This method is not implemented' );
	}
}
