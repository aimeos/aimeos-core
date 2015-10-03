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
class MW_Setup_Manager_Multiple extends MW_Setup_Manager_Base
{
	private $dbm;
	private $additional;
	private $tasks = array();
	private $tasksDone = array();
	private $dependencies = array();


	/**
	 * Initializes the setup manager.
	 *
	 * @param MW_DB_Manager_Iface $dbm Database manager
	 * @param array $dbconfig Associative list of database configurations, each with the db resource
	 * 	name as key and an associative list of "adapter", "host", "database", "username" and "password" keys
	 * @param array|string $taskpath Filesystem paths to the directory which contains the task classes
	 * @param mixed $additional Additionally provided information for the setup tasks if required
	 */
	public function __construct( MW_DB_Manager_Iface $dbm, array $dbconfig, $taskpath, $additional = null )
	{
		if( empty( $dbconfig ) ) {
			throw new MW_Setup_Exception( 'No databases configured in resource config file' );
		}

		$this->dbm = $dbm;
		$this->additional = $additional;

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
			$schemas[$rname] = $this->createSchema( $conns[$rname], $dbconf['adapter'], $dbconf['database'] );
		}

		if( !is_array( $taskpath ) ) { $taskpath = (array) $taskpath; }
		$this->setupTasks( $taskpath, $conns, $schemas );
	}


	/**
	 * Executes all tasks for the given database type
	 *
	 * @param string $dbtype Name of the database type (mysql, etc.)
	 */
	public function run( $dbtype )
	{
		foreach( $this->tasks as $taskname => $task ) {
			$this->runTasks( $dbtype, array( $taskname ) );
		}
	}


	/**
	 * Runs the given tasks depending on their dependencies.
	 *
	 * @param string $dbtype Database adapter type, e.g. "mysql", "pgsql", etc.
	 * @param array $tasknames List of task names
	 * @param array $stack List of task names that are sheduled after this task
	 */
	protected function runTasks( $dbtype, array $tasknames, array $stack = array() )
	{
		foreach( $tasknames as $taskname )
		{
			if( in_array( $taskname, $this->tasksDone ) ) { continue; }

			if( in_array( $taskname, $stack ) ) {
				$msg = 'Circular dependency for "%1$s" detected. Task stack: %2$s';
				throw new MW_Setup_Exception( sprintf( $msg, $taskname, implode( ', ', $stack ) ) );
			}

			$stack[] = $taskname;

			if( isset( $this->dependencies[$taskname] ) ) {
				$this->runTasks( $dbtype, (array) $this->dependencies[$taskname], $stack );
			}

			if( isset( $this->tasks[$taskname] ) ) {
				$this->tasks[$taskname]->run( $dbtype );
			}

			$this->tasksDone[] = $taskname;
		}
	}


	/**
	 * Sets up the tasks and their dependencies.
	 *
	 * @param array $paths List of paths containing setup task classes
	 * @param array $conns Associative list of db connections with the resource name as key
	 * @param array $schemas Associative list of db schemas with the resource name as key
	 */
	protected function setupTasks( array $paths, array $conns, array $schemas )
	{
		$defconn = ( isset( $conns['db'] ) ? $conns['db'] : reset( $conns ) );
		$defschema = ( isset( $schemas['db'] ) ? $schemas['db'] : reset( $schemas ) );

		$this->tasks = $this->createTasks( $paths, $defschema, $defconn, $this->additional );

		foreach( $this->tasks as $name => $task )
		{
			$task->setSchemas( $schemas );
			$task->setConnections( $conns );

			foreach( (array) $task->getPreDependencies() as $taskname ) {
				$this->dependencies[$name][] = $taskname;
			}

			foreach( (array) $task->getPostDependencies() as $taskname ) {
				$this->dependencies[$taskname][] = $name;
			}
		}
	}
}
