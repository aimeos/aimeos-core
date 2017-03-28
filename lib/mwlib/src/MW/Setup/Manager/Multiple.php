<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Setup
 */


namespace Aimeos\MW\Setup\Manager;


/**
 * Manager for setup and upgrade process using multiple connections.
 *
 * @package MW
 * @subpackage Setup
 */
class Multiple extends \Aimeos\MW\Setup\Manager\Base
{
	private $dbm;
	private $type;
	private $additional;
	private $tasks = [];
	private $tasksDone = [];
	private $dependencies = [];
	private $reverse = [];
	private $conns = [];


	/**
	 * Initializes the setup manager.
	 *
	 * @param \Aimeos\MW\DB\Manager\Iface $dbm Database manager
	 * @param array $dbconfig Associative list of database configurations, each with the db resource
	 * 	name as key and an associative list of "adapter", "host", "database", "username" and "password" keys
	 * @param array|string $taskpath Filesystem paths to the directory which contains the task classes
	 * @param mixed $additional Additionally provided information for the setup tasks if required
	 */
	public function __construct( \Aimeos\MW\DB\Manager\Iface $dbm, array $dbconfig, $taskpath, $additional = null )
	{
		parent::__construct( (array) $taskpath );

		if( empty( $dbconfig ) ) {
			throw new \Aimeos\MW\Setup\Exception( 'No databases configured in resource config file' );
		}

		$this->dbm = $dbm;
		$this->additional = $additional;
		$schemas = [];

		$this->type = ( isset( $dbconfig['db']['adapter'] ) ? $dbconfig['db']['adapter'] : '' );

		foreach( $dbconfig as $rname => $dbconf )
		{
			if( !isset( $dbconf['adapter'] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Configuration parameter "%1$s" missing in "%2$s"', 'adapter', $rname ) );
			}

			if( !isset( $dbconf['database'] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Configuration parameter "%1$s" missing in "%2$s"', 'database', $rname ) );
			}

			$this->conns[$rname] = $dbm->acquire( $rname );
			$schemas[$rname] = $this->createSchema( $this->conns[$rname], $dbconf['adapter'], $dbconf['database'] );
		}

		$this->setupTasks( (array) $taskpath, $this->conns, $schemas );
	}


	/**
	 * Cleans up the object
	 */
	public function __destruct()
	{
		foreach( $this->conns as $name => $conn ) {
			$this->dbm->release( $conn, $name );
		}

		unset( $this->dbm );
	}


	/**
	 * Cleans up old data required for roll back
	 *
	 * @param string|null $task Name of the task
	 */
	public function clean( $task = null )
	{
		$tasks = ( $task !== null && isset( $this->tasks[$task] ) ? array( $task => $this->tasks[$task] ) : $this->tasks );

		foreach( $tasks as $taskname => $task ) {
			$this->cleanTasks( array( $taskname ) );
		}
	}


	/**
	 * Updates the schema and migrates the data
	 *
	 * @param string|null $task Name of the task
	 */
	public function migrate( $task = null )
	{
		$tasks = ( $task !== null && isset( $this->tasks[$task] ) ? array( $task => $this->tasks[$task] ) : $this->tasks );

		foreach( $tasks as $taskname => $task ) {
			$this->migrateTasks( array( $taskname ) );
		}
	}


	/**
	 * Undo all schema changes and migrate data back
	 *
	 * @param string|null $task Name of the task
	 */
	public function rollback( $task = null )
	{
		$tasks = ( $task !== null && isset( $this->tasks[$task] ) ? array( $task => $this->tasks[$task] ) : $this->tasks );

		foreach( array_reverse( $tasks, true ) as $taskname => $task ) {
			$this->rollbackTasks( array( $taskname ) );
		}
	}


	/**
	 * Executes all tasks for the given database type
	 *
	 * @param string $dbtype Name of the database type (mysql, etc.)
	 * @deprecated 2016.05
	 */
	public function run( $dbtype )
	{
		$this->migrate();
	}


	/**
	 * Runs the clean method of the given tasks and their dependencies
	 *
	 * @param array $tasknames List of task names
	 * @param array $stack List of task names that are scheduled after this task
	 */
	protected function cleanTasks( array $tasknames, array $stack = [] )
	{
		foreach( $tasknames as $taskname )
		{
			if( in_array( $taskname, $this->tasksDone ) ) {
				continue;
			}

			if( in_array( $taskname, $stack ) )
			{
				$msg = 'Circular dependency for "%1$s" detected. Task stack: %2$s';
				throw new \Aimeos\MW\Setup\Exception( sprintf( $msg, $taskname, implode( ', ', $stack ) ) );
			}

			$stack[] = $taskname;

			if( isset( $this->tasks[$taskname] ) ) {
				$this->tasks[$taskname]->clean();
			}

			$this->tasksDone[] = $taskname;
		}
	}


	/**
	 * Runs the given tasks depending on their dependencies.
	 *
	 * @param array $tasknames List of task names
	 * @param array $stack List of task names that are sheduled after this task
	 */
	protected function migrateTasks( array $tasknames, array $stack = [] )
	{
		foreach( $tasknames as $taskname )
		{
			if( in_array( $taskname, $this->tasksDone ) ) {
				continue;
			}

			if( in_array( $taskname, $stack ) )
			{
				$msg = 'Circular dependency for "%1$s" detected. Task stack: %2$s';
				throw new \Aimeos\MW\Setup\Exception( sprintf( $msg, $taskname, implode( ', ', $stack ) ) );
			}

			$stack[] = $taskname;

			if( isset( $this->dependencies[$taskname] ) ) {
				$this->migrateTasks( (array) $this->dependencies[$taskname], $stack );
			}

			if( isset( $this->tasks[$taskname] ) )
			{
				$this->tasks[$taskname]->run( $this->type );
				$this->tasks[$taskname]->migrate();
			}

			$this->tasksDone[] = $taskname;
		}
	}


	/**
	 * Runs the rollback method of the given tasks and their dependencies
	 *
	 * @param array $tasknames List of task names
	 * @param array $stack List of task names that are sheduled after this task
	 */
	protected function rollbackTasks( array $tasknames, array $stack = [] )
	{
		foreach( $tasknames as $taskname )
		{
			if( in_array( $taskname, $this->tasksDone ) ) {
				continue;
			}

			if( in_array( $taskname, $stack ) )
			{
				$msg = 'Circular dependency for "%1$s" detected. Task stack: %2$s';
				throw new \Aimeos\MW\Setup\Exception( sprintf( $msg, $taskname, implode( ', ', $stack ) ) );
			}

			$stack[] = $taskname;

			if( isset( $this->reverse[$taskname] ) ) {
				$this->rollbackTasks( (array) $this->reverse[$taskname], $stack );
			}

			if( isset( $this->tasks[$taskname] ) ) {
				$this->tasks[$taskname]->rollback();
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

			foreach( (array) $task->getPreDependencies() as $taskname )
			{
				$this->dependencies[$name][] = $taskname;
				$this->reverse[$taskname][] = $name;
			}

			foreach( (array) $task->getPostDependencies() as $taskname )
			{
				$this->dependencies[$taskname][] = $name;
				$this->reverse[$name][] = $taskname;
			}
		}
	}
}
