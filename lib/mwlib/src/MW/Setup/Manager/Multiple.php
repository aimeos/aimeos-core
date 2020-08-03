<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2020
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

			$schemas[$rname] = $this->createSchema( $dbm, $rname, $dbconf['adapter'], $dbconf['database'] );
		}

		$this->setupTasks( (array) $taskpath, $schemas, $dbm );
	}


	/**
	 * Updates the schema and migrates the data
	 *
	 * @param string|null $task Name of the task
	 */
	public function migrate( string $task = null )
	{
		$this->tasksDone = [];
		$tasks = ( $task !== null && isset( $this->tasks[$task] ) ? array( $task => $this->tasks[$task] ) : $this->tasks );

		foreach( $tasks as $taskname => $task ) {
			$this->migrateTasks( array( $taskname ) );
		}
	}


	/**
	 * Runs the given tasks depending on their dependencies.
	 *
	 * @param string[] $tasknames List of task names
	 * @param string[] $stack List of task names that are sheduled after this task
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

			if( isset( $this->tasks[$taskname] ) ) {
				$this->tasks[$taskname]->migrate();
			}

			$this->tasksDone[] = $taskname;
		}
	}


	/**
	 * Sets up the tasks and their dependencies.
	 *
	 * @param string[] $paths List of paths containing setup task classes
	 * @param \Aimeos\MW\Setup\DBSchema\Iface[] $schemas Associative list of db schemas with the resource name as key
	 */
	protected function setupTasks( array $paths, array $schemas, \Aimeos\MW\DB\Manager\Iface $dbm )
	{
		$defschema = ( isset( $schemas['db'] ) ? $schemas['db'] : reset( $schemas ) );
		$this->tasks = $this->createTasks( $paths, $defschema, $dbm->acquire(), $this->additional );

		foreach( $this->tasks as $name => $task )
		{
			$task->setSchemas( $schemas );
			$task->setDatabaseManager( $dbm );

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
