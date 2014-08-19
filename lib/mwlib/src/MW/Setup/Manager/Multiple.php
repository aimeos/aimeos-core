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
class MW_Setup_Manager_Multiple extends MW_Setup_Manager_Abstract
{
	private $_dbm;
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
file_put_contents( 'setup.log', $taskname . "\n", FILE_APPEND );
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

		$this->_tasks = $this->_createTasks( $paths, $defschema, $defconn, $this->_additional );

		foreach( $this->_tasks as $name => $task )
		{
			$task->setSchemas( $schemas );
			$task->setConnections( $conns );

			foreach( (array) $task->getPreDependencies() as $taskname ) {
				$this->_dependencies[$name][] = $taskname;
			}

			foreach( (array) $task->getPostDependencies() as $taskname ) {
				$this->_dependencies[$taskname][] = $name;
			}
		}
	}
}
