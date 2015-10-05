<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Setup
 */


namespace Aimeos\MW\Setup\Manager;


/**
 * Manager for setup and upgrade processes.
 *
 * @package MW
 * @subpackage Setup
 */
class Standard extends \Aimeos\MW\Setup\Manager\Base
{
	private $conn;
	private $schema;
	private $additional;
	private $tasks = array();
	private $tasksDone = array();
	private $dependencies = array();


	/**
	 * Initializes the setup manager.
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param array $dbconfig Associative list with "adapter", "host", "database", "username" and "password" keys
	 * @param array|string $taskpath Filesystem paths to the directory which contains the task classes
	 * @param mixed $additional Additionally provided information for the setup tasks if required
	 */
	public function __construct( \Aimeos\MW\DB\Connection\Iface $conn, array $dbconfig, $taskpath, $additional = null )
	{
		foreach( array( 'adapter', 'database' )  as $key )
		{
			if( !isset( $dbconfig[$key] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Configuration parameter "%1$s" missing', $key ) );
			}
		}

		$this->conn = $conn;
		$this->schema = $this->createSchema( $conn, $dbconfig['adapter'], $dbconfig['database'] );
		$this->additional = $additional;

		if( !is_array( $taskpath ) ) { $taskpath = (array) $taskpath; }
		$this->setupTasks( $taskpath );
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
				throw new \Aimeos\MW\Setup\Exception( sprintf( $msg, $taskname, implode( ', ', $stack ) ) );
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
	 */
	protected function setupTasks( array $paths )
	{
		$this->tasks = $this->createTasks( $paths, $this->schema, $this->conn, $this->additional );

		foreach( $this->tasks as $name => $task )
		{
			foreach( (array) $task->getPreDependencies() as $taskname ) {
				$this->dependencies[$name][] = $taskname;
			}

			foreach( (array) $task->getPostDependencies() as $taskname ) {
				$this->dependencies[$taskname][] = $name;
			}
		}
	}
}
