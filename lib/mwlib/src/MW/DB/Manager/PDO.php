<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	private $config = null;
	private $connections = [];
	private $count = [];


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
	 * Clones the objects inside.
	 */
	public function __clone()
	{
		$this->config = clone $this->config;
	}


	/**
	 * Returns a database connection.
	 *
	 * @param string $name Name of the resource in configuration
	 * @return \Aimeos\MW\DB\Connection\Iface
	 */
	public function acquire( $name = 'db' )
	{
		try
		{
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
	public function release( \Aimeos\MW\DB\Connection\Iface $connection, $name = 'db' )
	{
		if( ( $connection instanceof \Aimeos\MW\DB\Connection\PDO ) === false ) {
			throw new \Aimeos\MW\DB\Exception( 'Connection object isn\'t of type \PDO' );
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
	protected function createConnection( $name, $adapter )
	{
		$host = $this->config->get( 'resource/' . $name . '/host' );
		$port = $this->config->get( 'resource/' . $name . '/port' );
		$user = $this->config->get( 'resource/' . $name . '/username' );
		$pass = $this->config->get( 'resource/' . $name . '/password' );
		$sock = $this->config->get( 'resource/' . $name . '/socket' );
		$dbase = $this->config->get( 'resource/' . $name . '/database' );

		$dsn = $adapter . ':dbname=' . $dbase;
		if( $sock == null )
		{
			$dsn .= isset( $host ) ? ';host=' . $host : '';
			$dsn .= isset( $port ) ? ';port=' . $port : '';
		}
		else
		{
			$dsn .= ';unix_socket=' . $sock;
		}

		$attr = array(
			\PDO::ATTR_PERSISTENT => $this->config->get( 'resource/' . $name . '/opt-persistent', false ),
		);

		$pdo = new \PDO( $dsn, $user, $pass, $attr );
		$pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
		$dbc = new \Aimeos\MW\DB\Connection\PDO( $pdo );

		foreach( $this->config->get( 'resource/' . $name . '/stmt', [] ) as $stmt ) {
			$dbc->create( $stmt )->execute()->finish();
		}

		return $dbc;
	}
}
