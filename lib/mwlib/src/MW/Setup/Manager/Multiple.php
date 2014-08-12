<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Setup
 */


/**
 * Manager for setup and upgrade process using multiple connections.
 *
 * @package MW
 * @subpackage Setup
 */
class MW_Setup_Manager_Multiple implements MW_Setup_Manager_Interface
{
	private $_additional;
	private $_tasks = array();
	private $_tasksDone = array();
	private $_dependencies = array();


	/**
	 * Initializes the setup manager.
	 *
	 * @param MW_DB_Manager_Interface $dbm Database manager
	 * @param array $dbconfig Associative list of database configurations, each with the db resource
	 * 	name as key and an associative list of "adapter", "host", "database", "username" and "password" keys
	 * @param array|string $taskpath Filesystem paths to the directory which contains the task classes
	 * @param mixed $additional Additionally provided information for the setup tasks if required
	 */
	public function __construct( MW_DB_Manager_Interface $dbm, array $dbconfig, $taskpath, $additional = null )
	{
		$this->_dbm = $dbm;
		$this->_additional = $additional;

		$conns = array();
		$schemas = array();

		foreach( $dbconfig as $rname => $dbconf )
		{
			if( !isset( $dbconf['adapter'] ) ) {
				throw new MW_Setup_Exception( sprintf( 'Configuration parameter "%1$s" missing in "%2$s"', 'adapter', $rname ) );
			}

			if( !isset( $dbconf['database'] ) ) {
				throw new MW_Setup_Exception( sprintf( 'Configuration parameter "%1$s" missing in "%2$s"', 'database', $rname ) );
			}

			$conns[$rname] = $dbm->acquire( $rname );
			$schemas[$rname] = $this->_createSchema( $conns[$rname], $dbconf['adapter'], $dbconf['database'] );
		}

		if( !is_array( $taskpath ) ) { $taskpath = (array) $taskpath; }
		$this->_setupTasks( $taskpath, $conns, $schemas );
	}


	/**
	 * Executes all tasks for the given database type
	 *
	 * @param string $dbtype Name of the database type (mysql, etc.)
	 */
	public function run( $dbtype )
	{
		foreach( $this->_tasks as $taskname => $task ) {
			$this->_runTasks( $dbtype, array( $taskname ) );
		}
	}


	/**
	 * Creates a new database schema object.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection
	 * @param string $adapter Database adapter, e.g. "mysql", "pgsql", etc.
	 * @param string $dbname Name of the database that will be used
	 * @return MW_Setup_DBSchema_Interface Database schema object
	 */
	protected  function _createSchema( MW_DB_Connection_Interface $conn, $adapter, $dbname )
	{
		if( empty( $adapter ) || ctype_alnum( $adapter ) === false ) {
			throw new MW_Setup_Exception( sprintf( 'Invalid database adapter "%1$s"', $adapter ) );
		}

		$classname = 'MW_Setup_DBSchema_' . ucwords( strtolower( $adapter ) );

		if( class_exists( $classname ) === false ) {
			throw new MW_Setup_Exception( sprintf( 'Database schema class "%1$s" not found', $classname ) );
		}

		return new $classname( $conn, $dbname );
	}


	/**
	 * Includes a PHP file.
	 *
	 * @param string $pathname Path to the file including the file name
	 */
	protected function _includeFile( $pathname )
	{
		if( ( include_once $pathname ) === false ) {
			throw new MW_Setup_Exception( sprintf( 'Unable to include file "%1$s"', $pathname ) );
		}
	}


	/**
	 * Runs the given tasks depending on their dependencies.
	 *
	 * @param string $dbtype Database adapter type, e.g. "mysql", "pgsql", etc.
	 * @param array $tasknames List of task names
	 * @param array $stack List of task names that are sheduled after this task
	 */
	protected function _runTasks( $dbtype, array $tasknames, array $stack = array() )
	{
		foreach( $tasknames as $taskname )
		{
			if( in_array( $taskname, $this->_tasksDone ) ) { continue; }

			if( in_array( $taskname, $stack ) ) {
				$msg = 'Circular dependency for "%1$s" detected. Task stack: %2$s';
				throw new MW_Setup_Exception( sprintf( $msg, $taskname, implode( ', ', $stack ) ) );
			}

			$stack[] = $taskname;

			if( isset( $this->_dependencies[$taskname] ) ) {
				$this->_runTasks( $dbtype, (array) $this->_dependencies[$taskname], $stack );
			}

			if( isset( $this->_tasks[$taskname] ) ) {
				$this->_tasks[$taskname]->run( $dbtype );
			}

			$this->_tasksDone[] = $taskname;
		}
	}


	/**
	 * Sets up the tasks and their dependencies.
	 *
	 * @param array $paths List of paths containing setup task classes
	 * @param array $conns Associative list of db connections with the resource name as key
	 * @param array $schemas Associative list of db schemas with the resource name as key
	 */
	protected function _setupTasks( array $paths, array $conns, array $schemas )
	{
		$defconn = ( isset( $conns['db'] ) ? $conns['db'] : reset( $conns ) );
		$defschema = ( isset( $schemas['db'] ) ? $schemas['db'] : reset( $schemas ) );

		foreach( $paths as $path )
		{
			foreach( new DirectoryIterator( $path ) as $item )
			{
				if( $item->isDir() === true || substr( $item->getFilename(), -4 ) != '.php' ) { continue; }

				$this->_includeFile( $item->getPathName() );

				$taskname = substr( $item->getFilename(), 0, -4 );
				$classname = 'MW_Setup_Task_' . $taskname;

				if( class_exists( $classname ) === false ) {
					throw new MW_Setup_Exception( sprintf( 'Class "%1$s" not found', $classname ) );
				}

				$interface = 'MW_Setup_Task_Interface';
				$task = new $classname( $defschema, $defconn, $this->_additional );
				$task->setConnections( $conns );
				$task->setSchemas( $schemas );

				if( ( $task instanceof $interface ) === false ) {
					throw new MW_Setup_Exception( sprintf( 'Class "%1$s" doesn\'t implement "%2$s"', $classname, 'MW_Setup_Task_Interface' ) );
				}

				$this->_tasks[$taskname] = $task;
				$this->_dependencies[$taskname] = (array) $task->getPreDependencies();
			}
		}

		foreach( $this->_tasks as $name => $task )
		{
			foreach( (array) $task->getPostDependencies() as $taskname ) {
				$this->_dependencies[$taskname][] = $name;
			}
		}
	}
}
