<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Setup
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Base class for all setup tasks
 *
 * @package MW
 * @subpackage Setup
 */
abstract class Base implements \Aimeos\MW\Setup\Task\Iface
{
	private $connections = [];
	private $schemas = [];
	private $paths = [];
	protected $additional;

	/** @deprecated Use getSchema() instead */
	protected $schema;

	/** @deprecated Use getConnection() instead */
	protected $conn;


	/**
	 * Initializes the task object.
	 *
	 * @param \Aimeos\MW\Setup\DBSchema\Iface $schema Database schema object
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param mixed $additional Additionally provided information for the setup tasks if required
	 * @param array $paths List of paths of the setup tasks ordered by dependencies
	 */
	public function __construct( \Aimeos\MW\Setup\DBSchema\Iface $schema, \Aimeos\MW\DB\Connection\Iface $conn,
		$additional = null, array $paths = [] )
	{
		$this->schema = $schema;
		$this->conn = $conn;
		$this->paths = $paths;
		$this->additional = $additional;
	}


	/**
	 * Updates the schema and migrates the data
	 *
	 * @return void
	 */
	public function migrate()
	{
	}


	/**
	 * Undo all schema changes and migrate data back
	 *
	 * @return void
	*/
	public function rollback()
	{
	}


	/**
	 * Cleans up old data required for roll back
	 *
	 * @return void
	*/
	public function clean()
	{
	}


	/**
	 * Executes the task for the given database type.
	 *
	 * @param string $dbtype Database type string
	 * @deprecated Use migrate() instead
	 */
	public function run( $dbtype )
	{
		if( $dbtype === 'mysql' && method_exists( $this, 'mysql' ) ) {
			$this->mysql();
		}
	}


	/**
	 * Sets the associative list of connections with the resource name as key.
	 *
	 * @param array $conns Associative list of connections
	 */
	public function setConnections( array $conns )
	{
		$this->connections = $conns;
	}


	/**
	 * Sets the associative list of schemas with the resource name as key.
	 *
	 * @param array $schemas Associative list of schemas
	 */
	public function setSchemas( array $schemas )
	{
		$this->schemas = $schemas;
	}


	/**
	 * Executes a given SQL statement.
	 *
	 * @param string $sql SQL statement to execute
	 * @param string $name Name from the resource configuration
	 */
	protected function execute( $sql, $name = 'db' )
	{
		$this->getConnection( $name )->create( $sql )->execute()->finish();
	}


	/**
	 * Executes a list of given SQL statements.
	 *
	 * @param array $list List of SQL statement to execute
	 * @param string $name Name from the resource configuration
	 */
	protected function executeList( array $list, $name = 'db' )
	{
		$conn = $this->getConnection( $name );

		foreach( $list as $sql ) {
			$conn->create( $sql )->execute()->finish();
		}
	}


	/**
	 * Returns the connection specified by the given resource name.
	 *
	 * @param string $name Name from resource configuration
	 * @return \Aimeos\MW\DB\Connection\Iface
	 */
	protected function getConnection( $name )
	{
		if( !isset( $this->connections[$name] ) ) {
			return $this->conn;
		}

		return $this->connections[$name];
	}


	/**
	 * Returns the schemas specified by the given resource name.
	 *
	 * @param string $name Name from resource configuration
	 * @return \Aimeos\MW\Setup\DBSchema\Iface
	 */
	protected function getSchema( $name )
	{
		if( !isset( $this->schemas[$name] ) ) {
			return $this->schema;
		}

		return $this->schemas[$name];
	}


	/**
	 * Returns the setup task paths ordered by their dependencies
	 *
	 * @return array List of file system paths
	 */
	protected function getSetupPaths()
	{
		return $this->paths;
	}


	/**
	 * Executes a given SQL statement and returns the value of the named column and first row.
	 *
	 * @param string $sql SQL statement to execute
	 * @param string $column Column name to retrieve
	 * @param string $name Name from the resource configuration
	 * @return string Column value
	 */
	protected function getValue( $sql, $column, $name = 'db' )
	{
		$result = $this->getConnection( $name )->create( $sql )->execute();

		if( ( $row = $result->fetch() ) === false ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'No rows found: %1$s', $sql ) );
		}

		if( array_key_exists( $column, $row ) === false ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'No column "%1$s" found: %2$s', $column, $sql ) );
		}

		$result->finish();

		return $row[$column];
	}


	/**
	 * Prints the message for the current test.
	 *
	 * @param string $msg Current message
	 * @param integer $level Indent level of the message (default: 0 )
	 */
	protected function msg( $msg, $level = 0 )
	{
		$pre = '';
		for( $i = 0; $i < 2*$level; $i++ ) {
			$pre .= ' ';
		}

		echo str_pad( $pre . $msg, 70 );
	}


	/**
	 * Prints the status for the current test.
	 *
	 * @param string $status Current status
	 */
	protected function status( $status )
	{
		echo $status . PHP_EOL;
	}


	/**
	 * Extracts the table definitions from the given content.
	 *
	 * @param string $content Content of the file to parse
	 * @return array Associative list of table names with table create statements ordered like in the file
	 */
	protected function getTableDefinitions( $content )
	{
		$defs = [];
		$matches = [];

		$regex = '/CREATE TABLE \"?([a-zA-Z0-9_]+)\"? .*(\n\n|$)/sU';
		if ( preg_match_all($regex, $content, $matches, PREG_SET_ORDER) === false ) {
			throw new \Aimeos\MW\Setup\Exception('Unable to get table definitions');
		}

		foreach ( $matches as $match ) {
			$defs[$match[1]] = $match[0];
		}

		return $defs;
	}


	/**
	 * Extracts the index definitions from the given content.
	 *
	 * @param string $content Content of the file to parse
	 * @return array Associative list of index names with index create statements ordered like in the file
	 */
	protected function getIndexDefinitions( $content )
	{
		$defs = [];
		$matches = [];

		if ( preg_match_all('/CREATE [a-zA-Z]* ?INDEX \"?([a-zA-Z0-9_]+)\"? ON \"?([a-zA-Z0-9_]+)\"? .+(\n\n|$)/sU', $content, $matches, PREG_SET_ORDER) === false ) {
			throw new \Aimeos\MW\Setup\Exception('Unable to get index definitions');
		}

		foreach ( $matches as $match ) {
			$name = $match[2] . '.' . $match[1];
			$defs[$name] = $match[0];
		}

		return $defs;
	}


	/**
	 * Extracts the trigger definitions from the given content.
	 *
	 * @param string $content Content of the file to parse
	 * @return array Associative list of trigger names with trigger create statements ordered like in the file
	 */
	protected function getTriggerDefinitions( $content )
	{
		$defs = [];
		$matches = [];

		$regex = '/CREATE TRIGGER \"?([a-zA-Z0-9_]+)\"? .*(\n\n|$)/sU';
		if ( preg_match_all($regex, $content, $matches, PREG_SET_ORDER) === false ) {
			throw new \Aimeos\MW\Setup\Exception('Unable to get trigger definitions');
		}

		foreach ( $matches as $match ) {
			$defs[$match[1]] = $match[0];
		}

		return $defs;
	}
}
