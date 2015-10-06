<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 */


/**
 * Manager for database connections using the PDO library.
 *
 * @package MW
 * @subpackage DB
 */
class MW_DB_Manager_PDO implements MW_DB_Manager_Interface
{
	private $config = null;
	private $connections = array();
	private $count = array();


	/**
	 * Initializes the database manager object
	 *
	 * @param MW_Config_Interface $config Object holding the configuration data
	 */
	public function __construct( MW_Config_Interface $config )
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
	 * @return MW_DB_Connection_Interface
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

				if( $limit >= 0 && $this->count[$name] >= $limit ) {
					throw new MW_DB_Exception( sprintf( 'Maximum number of connections (%1$d) exceeded', $limit ) );
				}

				$this->connections[$name] = array( $this->createConnection( $name, $adapter ) );
				$this->count[$name]++;
			}

			switch( $adapter )
			{
				case 'sqlite':
				case 'sqlite3':
					// SQLite uses page locking which prevents a second connection from
					// reading from tables which are already in use. Fortunately, it is
					// possible withing the same connection to do the update.
					return $this->connections[$name][0];
			}

			return array_pop( $this->connections[$name] );

		}
		catch( PDOException $e ) {
			throw new MW_DB_Exception( $e->getMessage(), $e->getCode(), $e->errorInfo );
		}
	}


	/**
	 * Releases the connection for reuse
	 *
	 * @param MW_DB_Connection_Interface $connection Connection object
	 * @param string $name Name of resource
	 */
	public function release( MW_DB_Connection_Interface $connection, $name = 'db' )
	{
		if( ( $connection instanceof MW_DB_Connection_PDO ) === false ) {
			throw new MW_DB_Exception( 'Connection object isn\'t of type PDO' );
		}

		$this->connections[$name][] = $connection;
	}


	/**
	 * Creates a new database connection.
	 *
	 * @param string $name Name to the database configuration in the resource file
	 * @param string $adapter Name of the database adapter, e.g. "mysql"
	 * @return MW_DB_Connection_Interface Database connection
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
			PDO::ATTR_PERSISTENT => $this->config->get( 'resource/' . $name . '/opt-persistent', false ),
		);

		$pdo = new PDO( $dsn, $user, $pass, $attr );
		$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$dbc = new MW_DB_Connection_PDO( $pdo );

		foreach( $this->config->get( 'resource/' . $name . '/stmt', array() ) as $stmt ) {
			$dbc->create( $stmt )->execute()->finish();
		}

		return $dbc;
	}
}
