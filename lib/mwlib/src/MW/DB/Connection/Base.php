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
 * Common class for all database connection implementations.
 *
 * @package MW
 * @subpackage DB
 */
abstract class Base
{
	private $params;


	/**
	 * Initializes the base class
	 *
	 * @param array $params Connection parameters
	 */
	public function __construct( array $params = [] )
	{
		$this->params = $params;
	}


	/**
	 * Returns the underlying connection object
	 *
	 * @return mixed Underlying connection object
	 */
	abstract public function getRawObject();


	/**
	 * Escapes the value if necessary for direct inclusion in SQL statement.
	 *
	 * @param string|null $data Value to escape or null for no value
	 * @return string Escaped string
	 */
	public function escape( string $data = null ) : string
	{
		$quoted = $this->getRawObject()->quote( $data );

		if( $quoted[0] === '\'' ) {
			$quoted = substr( $quoted, 1, strlen( $quoted ) - 2 );
		}

		return $quoted;
	}


	/**
	 * Returns the connection parameters
	 *
	 * @return array Parameters to connect to the database server
	 */
	protected function getParameters() : array
	{
		return $this->params;
	}


	/**
	 * Checks if a transaction is currently running
	 *
	 * @return bool True if transaction is currently running, false if not
	 */
	public function inTransaction() : bool
	{
		return true; // safe default
	}
}
