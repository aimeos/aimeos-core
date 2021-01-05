<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Manager;


/**
 * Manager for database connections using the DBAL library
 *
 * @package MW
 * @subpackage DB
 */
class DBAL implements \Aimeos\MW\DB\Manager\Iface
{
	private $connections = [];
	private $count = [];
	private $config;


	/**
	 * Initializes the database manager object
	 *
	 * @param \Aimeos\MW\Config\Iface $config Object holding the configuration data
	 */
	public function __construct( \Aimeos\MW\Config\Iface $config )
	{
		$this->config = $config;
	}


	/**
	 * Cleans up the object
	 */
	public function __destruct()
	{
		foreach( $this->connections as $name => $list )
		{
			foreach( $list as $key => $conn ) {
				unset( $this->connections[$name][$key] );
			}
		}
	}


	/**
	 * Reset when cloning the object
	 */
	public function __clone()
	{
		$this->connections = [];
		$this->count = [];
	}


	/**
	 * Clean up the objects inside
	 */
	public function __sleep()
	{
		$this->__destruct();

		$this->connections = [];
		$this->count = [];

		return get_object_vars( $this );
	}


	/**
	 * Returns a database connection.
	 *
	 * @param string $name Name of the resource in configuration
	 * @return \Aimeos\MW\DB\Connection\Iface
	 */
	public function acquire( string $name = 'db' )
	{
		try
		{
			if( $this->config->get( 'resource/' . $name ) === null ) {
				$name = 'db';
			}

			$adapter = $this->config->get( 'resource/' . $name . '/adapter', 'mysql' );

			if( !isset( $this->connections[$name] ) || empty( $this->connections[$name] ) )
			{
				if( !isset( $this->count[$name] ) ) {
					$this->count[$name] = 0;
				}

				$limit = $this->config->get( 'resource/' . $name . '/limit', -1 );

				if( $limit >= 0 && $this->count[$name] >= $limit )
				{
					$msg = sprintf( 'Maximum number of connections (%1$d) for "%2$s" exceeded', $limit, $name );
					throw new \Aimeos\MW\DB\Exception( $msg );
				}

				$this->connections[$name] = array( $this->createConnection( $name, $adapter ) );
				$this->count[$name]++;
			}

			return array_pop( $this->connections[$name] );
		}
		catch( \Exception $e ) {
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode() );
		}
	}


	/**
	 * Releases the connection for reuse
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $connection Connection object
	 * @param string $name Name of resource
	 */
	public function release( \Aimeos\MW\DB\Connection\Iface $connection, string $name = 'db' )
	{
		if( ( $connection instanceof \Aimeos\MW\DB\Connection\DBAL ) === false ) {
			throw new \Aimeos\MW\DB\Exception( 'Connection object isn\'t of type DBAL' );
		}

		if( $this->config->get( 'resource/' . $name ) === null ) {
			$name = 'db';
		}

		$this->connections[$name][] = $connection;
	}


	/**
	 * Creates a new database connection.
	 *
	 * @param string $name Name to the database configuration in the resource file
	 * @param string $adapter Name of the database adapter, e.g. "mysql"
	 * @return \Aimeos\MW\DB\Connection\Iface Database connection
	 */
	protected function createConnection( string $name, string $adapter ) : \Aimeos\MW\DB\Connection\Iface
	{
		$params = $this->config->get( 'resource/' . $name, [] );

		$params['user'] = $this->config->get( 'resource/' . $name . '/username' );
		$params['dbname'] = $this->config->get( 'resource/' . $name . '/database' );

		if( ( $socket = $this->config->get( 'resource/' . $name . '/socket' ) ) != null ) {
			$params['unix_socket'] = $socket;
		}

		switch( $adapter )
		{
			case 'mysql': $params['driver'] = 'pdo_mysql'; break;
			case 'oracle': $params['driver'] = 'oci8'; break;
			case 'pgsql': $params['driver'] = 'pdo_pgsql'; break;
			case 'sqlite': $params['driver'] = 'pdo_sqlite'; break;
			case 'sqlsrv': $params['driver'] = 'pdo_sqlsrv'; break;
			default: $params['driver'] = $adapter;
		}

		$stmts = $this->config->get( 'resource/' . $name . '/stmt', [] );

		return new \Aimeos\MW\DB\Connection\DBAL( $params, $stmts );
	}
}
