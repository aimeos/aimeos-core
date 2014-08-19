<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Setup
 */


/**
 * Manager for setup and upgrade processes.
 *
 * @package MW
 * @subpackage Setup
 */
class MW_Setup_Manager_Default extends MW_Setup_Manager_Abstract
{
	private $_conn;
	private $_schema;
	private $_additional;
	private $_tasks = array();
	private $_tasksDone = array();
	private $_dependencies = array();


	/**
	 * Initializes the setup manager.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection
	 * @param array $dbconfig Associative list with "adapter", "host", "database", "username" and "password" keys
	 * @param array|string $taskpath Filesystem paths to the directory which contains the task classes
	 * @param mixed $additional Additionally provided information for the setup tasks if required
	 */
	public function __construct( MW_DB_Connection_Interface $conn, array $dbconfig, $taskpath, $additional = null )
	{
		foreach( array( 'adapter', 'database' )  as $key )
		{
			if( !isset( $dbconfig[$key] ) ) {
				throw new MW_Setup_Exception( sprintf( 'Configuration parameter "%1$s" missing', $key ) );
			}
		}

		$this->_conn = $conn;
		$this->_schema = $this->_createSchema( $conn, $dbconfig['adapter'], $dbconfig['database'] );
		$this->_additional = $additional;

		if( !is_array( $taskpath ) ) { $taskpath = (array) $taskpath; }
		$this->_setupTasks( $taskpath );
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
				$this->_tasks[$taskname]->run( $dbtype );
			}

			$this->_tasksDone[] = $taskname;
		}
	}


	/**
	 * Sets up the tasks and their dependencies.
	 *
	 * @param array $paths List of paths containing setup task classes
	 */
	protected function _setupTasks( array $paths )
	{
		$this->_tasks = $this->_createTasks( $paths, $this->_schema, $this->_conn, $this->_additional );

		foreach( $this->_tasks as $name => $task )
		{
			foreach( (array) $task->getPreDependencies() as $taskname ) {
				$this->_dependencies[$name][] = $taskname;
			}

			foreach( (array) $task->getPostDependencies() as $taskname ) {
				$this->_dependencies[$taskname][] = $name;
			}
		}
	}
}
