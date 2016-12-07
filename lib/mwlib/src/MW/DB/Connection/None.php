<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Connection;


/**
 * Dummy database connection class.
 *
 * @package MW
 * @subpackage DB
 */
class None
	extends \Aimeos\MW\DB\Connection\Base
	implements \Aimeos\MW\DB\Connection\Iface
{
	/**
	 * Creates a database statement.
	 *
	 * Throws an exception because there is no implementation available.
	 *
	 * @param string $sql SQL statement, maybe with place holders
	 * @param integer $type Simple or prepared statement type constant from abstract class
	 * @throws \Aimeos\MW\DB\Exception
	 */
	public function create( $sql, $type = \Aimeos\MW\DB\Connection\Base::TYPE_SIMPLE )
	{
		throw new \Aimeos\MW\DB\Exception( 'This method is not implemented' );
	}


	/**
	 * Returns the underlying connection object
	 *
	 * Throws an exception because there is no implementation available.
	 *
	 * @throws \Aimeos\MW\DB\Exception
	 */
	public function getRawObject()
	{
		throw new \Aimeos\MW\DB\Exception( 'This method is not implemented' );
	}


	/**
	 * Starts a transaction for this connection.
	 *
	 * Throws an exception because there is no implementation available.
	 *
	 * @throws \Aimeos\MW\DB\Exception
	 */
	public function begin()
	{
		throw new \Aimeos\MW\DB\Exception( 'This method is not implemented' );
	}


	/**
	 * Commits the changes done inside of the transaction to the storage.
	 *
	 * Throws an exception because there is no implementation available.
	 *
	 * @throws \Aimeos\MW\DB\Exception
	 */
	public function commit()
	{
		throw new \Aimeos\MW\DB\Exception( 'This method is not implemented' );
	}


	/**
	 * Discards the changes done inside of the transaction.
	 *
	 * Throws an exception because there is no implementation available.
	 *
	 * @throws \Aimeos\MW\DB\Exception
	 */
	public function rollback()
	{
		throw new \Aimeos\MW\DB\Exception( 'This method is not implemented' );
	}
}