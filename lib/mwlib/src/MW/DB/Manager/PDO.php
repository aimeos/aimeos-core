<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Manager;


/**
 * Manager for database connections using the \PDO library.
 *
 * @package MW
 * @subpackage DB
 */
class PDO implements \Aimeos\MW\DB\Manager\Iface
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
	public function acquire( string $name = 'db' ) : \Aimeos\MW\DB\Connection\Iface
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
		catch( \PDOException $e ) {
			throw new \Aimeos\MW\DB\Exception( $e->getMessage(), $e->getCode(), $e->errorInfo );
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
		if( ( $connection instanceof \Aimeos\MW\DB\Connection\PDO ) === false ) {
			throw new \Aimeos\MW\DB\Exception( 'Connection object isn\'t of type \PDO' );
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
		$params = $this->config->get( 'resource/' . $name );

		if( !isset( $params['dsn'] ) )
		{
			$host = $this->config->get( 'resource/' . $name . '/host' );
			$port = $this->config->get( 'resource/' . $name . '/port' );
			$sock = $this->config->get( 'resource/' . $name . '/socket' );
			$dbase = $this->config->get( 'resource/' . $name . '/database' );

			$dsn = $adapter . ':';

			if( $adapter === 'sqlsrv' )
			{
				$dsn .= 'Database=' . $dbase;
				$dsn .= isset( $host ) ? ';Server=' . $host . ( isset( $port ) ? ',' . $port : '' ) : '';
			}
			elseif( $sock == null )
			{
				$dsn .= 'dbname=' . $dbase;
				$dsn .= isset( $host ) ? ';host=' . $host : '';
				$dsn .= isset( $port ) ? ';port=' . $port : '';
			}
			else
			{
				$dsn .= 'dbname=' . $dbase . ';unix_socket=' . $sock;
			}

			$params['dsn'] = $dsn;
		}

		$stmts = $this->config->get( 'resource/' . $name . '/stmt', [] );

		return new \Aimeos\MW\DB\Connection\PDO( $params, $stmts );
	}
}
