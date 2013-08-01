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
	private $_config = null;
	private $_connections = array();
	private $_count = array();


	/**
	 * Initializes the database manager object
	 *
	 * @param MW_Config_Interface $config Object holding the configuration data
	 */
	public function __construct( MW_Config_Interface $config )
	{
		$this->_config = $config;
	}


	/**
	 * Clones the objects inside.
	 */
	public function __clone()
	{
		$this->_config = clone $this->_config;
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
			$adapter = $this->_config->get( 'resource/' . $name . '/adapter', 'mysql' );

			if( !isset( $this->_connections[$name] ) || empty( $this->_connections[$name] ) )
			{
				if( !isset( $this->_count[$name] ) ) {
					$this->_count[$name] = 0;
				}

				$limit = $this->_config->get( 'resource/' . $name . '/limit', -1 );

				if( $limit >= 0 && $this->_count[$name] >= $limit ) {
					throw new MW_DB_Exception( sprintf( 'Maximum number of connections (%1$d) exceeded', $limit ) );
				}

				$host = $this->_config->get( 'resource/' . $name . '/host' );
				$port = $this->_config->get( 'resource/' . $name . '/port' );
				$user = $this->_config->get( 'resource/' . $name . '/username' );
				$pass = $this->_config->get( 'resource/' . $name . '/password' );
				$sock = $this->_config->get( 'resource/' . $name . '/socket' );
				$dbase = $this->_config->get( 'resource/' . $name . '/database' );

				$dsn = $adapter . ':dbname=' . $dbase;
				if( $sock !== null )
				{
					$dsn .= isset( $host ) ? ';host=' . $host : '';
					$dsn .= isset( $port ) ? ';port=' . $port : '';
				}
				else
				{
					$dsn .= ';unix_socket=' . $sock;
				}

				$attr = array(
					PDO::ATTR_PERSISTENT => $this->_config->get( 'resource/' . $name . '/opt-persistent', false ),
				);

				$pdo = new PDO( $dsn, $user, $pass, $attr );
				$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
				$dbc = new MW_DB_Connection_PDO( $pdo );

				foreach( $this->_config->get( 'resource/' . $name . '/stmt', array() ) as $stmt ) {
					$dbc->create( $stmt )->execute()->finish();
				}

				$this->_connections[$name] = array( $dbc );
				$this->_count[$name]++;
			}

			switch( $adapter )
			{
				case 'sqlite':
				case 'sqlite3':
					// SQLite uses page locking which prevents a second connection from
					// reading from tables which are already in use. Fortunately, it is
					// possible withing the same connection to do the update.
					return $this->_connections[$name][0];
			}

			return array_pop( $this->_connections[$name] );

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

		$this->_connections[$name][] = $connection;
	}
}
