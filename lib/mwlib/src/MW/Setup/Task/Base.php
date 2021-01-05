<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	private $dbm;
	private $paths = [];
	private $schemas = [];
	private $connections = [];
	protected $additional;

	/** @deprecated Use getSchema() instead */
	protected $schema;

	/** @deprecated Use acquire() and release() instead */
	protected $conn;


	/**
	 * Initializes the task object.
	 *
	 * @param \Aimeos\MW\Setup\DBSchema\Iface $schema Database schema object
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param mixed $additional Additionally provided information for the setup tasks if required
	 * @param string[] $paths List of paths of the setup tasks ordered by dependencies
	 */
	public function __construct( \Aimeos\MW\Setup\DBSchema\Iface $schema, \Aimeos\MW\DB\Connection\Iface $conn,
		$additional = null, array $paths = [] )
	{
		$this->connections['db'] = $conn;
		$this->schema = $schema;
		$this->conn = $conn;
		$this->paths = $paths;
		$this->additional = $additional;
	}


	/**
	 * Returns the list of task names which this task depends on
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return [];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies() : array
	{
		return [];
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
	 * Sets the database manager object
	 *
	 * @param \Aimeos\MW\DB\Manager\Iface $dbm Database manager
	 * @return \Aimeos\MW\Setup\Task\Iface Task object for fluent interface
	 */
	public function setDatabaseManager( \Aimeos\MW\DB\Manager\Iface $dbm ) : Iface
	{
		$this->dbm = $dbm;
		return $this;
	}


	/**
	 * Sets the associative list of schemas with the resource name as key.
	 *
	 * @param \Aimeos\MW\Setup\DBSchema\Iface[] $schemas Associative list of schemas
	 * @return \Aimeos\MW\Setup\Task\Iface Task object for fluent interface
	 */
	public function setSchemas( array $schemas ) : Iface
	{
		$this->schemas = $schemas;
		return $this;
	}


	/**
	 * Returns the database connection
	 *
	 * @param string $name Name from the resource configuration
	 * @return \Aimeos\MW\DB\Connection\Iface Database connection
	 */
	protected function acquire( $name = 'db' ) : \Aimeos\MW\DB\Connection\Iface
	{
		return $this->dbm->acquire( $name );
	}


	/**
	 * Releases the database connection
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param string $name Name from the resource configuration
	 */
	protected function release( \Aimeos\MW\DB\Connection\Iface $conn, $name = 'db' )
	{
		return $this->dbm->release( $conn, $name );
	}


	/**
	 * Executes a given SQL statement.
	 *
	 * @param string $sql SQL statement to execute
	 * @param string $name Name from the resource configuration
	 * @return \Aimeos\MW\Setup\Task\Iface Task object for fluent interface
	 */
	protected function execute( string $sql, string $name = 'db' ) : Iface
	{
		$conn = $this->acquire( $name );
		$conn->create( $sql )->execute()->finish();
		$this->release( $conn, $name );
		return $this;
	}


	/**
	 * Executes a list of given SQL statements.
	 *
	 * @param string[] $list List of SQL statement to execute
	 * @param string $name Name from the resource configuration
	 * @return \Aimeos\MW\Setup\Task\Iface Task object for fluent interface
	 */
	protected function executeList( array $list, string $name = 'db' ) : Iface
	{
		$conn = $this->acquire( $name );

		foreach( $list as $sql ) {
			$conn->create( $sql )->execute()->finish();
		}

		$this->release( $conn, $name );
		return $this;
	}


	/**
	 * Returns the schemas specified by the given resource name.
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Connection with insert statement executed at last
	 * @param string $rname Resource name of the connection the table belongs to
	 * @param string|null $sequence Name of the sequence which generated the last ID (only Oracle)
	 * @return string|null Last inserted ID or null if not available
	 */
	protected function getLastId( \Aimeos\MW\DB\Connection\Iface $conn, string $rname, string $sequence = null ) : ?string
	{
		$adapter = $this->getSchema( $rname )->getName();
		$map = [
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT ' . $sequence . '.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		];

		if( !isset( $map[$adapter] ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Unsupported adapter: %1$s', $adapter ) );
		}

		$result = $conn->create( str_replace( ':seq:', $sequence, $map[$adapter] ) )->execute();
		$row = $result->fetch( \Aimeos\MW\DB\Result\Base::FETCH_NUM );
		$result->finish();

		return $row && isset( $row[0] ) ? $row[0] : null;
	}


	/**
	 * Returns the schemas specified by the given resource name.
	 *
	 * @param string $name Name from resource configuration
	 * @return \Aimeos\MW\Setup\DBSchema\Iface
	 */
	protected function getSchema( string $name ) : \Aimeos\MW\Setup\DBSchema\Iface
	{
		if( !isset( $this->schemas[$name] ) ) {
			return $this->schema;
		}

		return $this->schemas[$name];
	}


	/**
	 * Returns the DBAL schema manager for the given resource name
	 *
	 * @param string $name Name from resource configuration
	 * @return \Doctrine\DBAL\Schema\AbstractSchemaManager DBAL schema manager
	 */
	protected function getSchemaManager( string $rname ) : \Doctrine\DBAL\Schema\AbstractSchemaManager
	{
		$conn = $this->acquire( $rname );
		$dbal = $conn->getRawObject();
		$this->release( $conn, $rname );

		if( !( $dbal instanceof \Doctrine\DBAL\Connection ) ) {
			throw new \Aimeos\MW\Setup\Exception( 'Not a DBAL connection' );
		}

		return $dbal->getSchemaManager();
	}


	/**
	 * Returns the setup task paths ordered by their dependencies
	 *
	 * @return string[] List of file system paths
	 */
	protected function getSetupPaths() : array
	{
		return $this->paths;
	}


	/**
	 * Executes a given SQL statement and returns the value of the named column and first row.
	 *
	 * @param string $sql SQL statement to execute
	 * @param string $column Column name to retrieve
	 * @param string $name Name from the resource configuration
	 * @return mixed Column value
	 */
	protected function getValue( string $sql, string $column, string $name = 'db' )
	{
		$conn = $this->acquire( $name );

		try
		{
			$result = $conn->create( $sql )->execute();

			if( ( $row = $result->fetch() ) === null ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No rows found: %1$s', $sql ) );
			}

			$result->finish();

			if( array_key_exists( $column, $row ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No column "%1$s" found: %2$s', $column, $sql ) );
			}

			$this->release( $conn, $name );
		}
		catch( \Exception $e )
		{
			$this->release( $conn, $name );
			throw $e;
		}

		return $row[$column];
	}


	/**
	 * Prints the message for the current test.
	 *
	 * @param string $msg Current message
	 * @param int $level Indent level of the message (default: 0 )
	 * @param string|null $status Current status
	 * @return \Aimeos\MW\Setup\Task\Iface Task object for fluent interface
	 */
	protected function msg( string $msg, int $level = 0, string $status = null ) : Iface
	{
		$pre = '';
		for( $i = 0; $i < 2 * $level; $i++ ) {
			$pre .= ' ';
		}

		echo str_pad( $pre . $msg, 70 ) . ( $status !== null ? $status . PHP_EOL : '' );
		return $this;
	}


	/**
	 * Prints the status for the current test.
	 *
	 * @param string $status Current status
	 * @return \Aimeos\MW\Setup\Task\Iface Task object for fluent interface
	 */
	protected function status( string $status ) : Iface
	{
		echo $status . PHP_EOL;
		return $this;
	}


	/**
	 * Extracts the table definitions from the given content.
	 *
	 * @param string $content Content of the file to parse
	 * @return string[] Associative list of table names with table create statements ordered like in the file
	 */
	protected function getTableDefinitions( string $content ) : array
	{
		$defs = [];
		$matches = [];

		$regex = '/CREATE TABLE \"?([a-zA-Z0-9_]+)\"? .*([\r\n]{2,4}|$)/sU';
		if( preg_match_all( $regex, $content, $matches, PREG_SET_ORDER ) === false ) {
			throw new \Aimeos\MW\Setup\Exception( 'Unable to get table definitions' );
		}

		foreach( $matches as $match ) {
			$defs[$match[1]] = $match[0];
		}

		return $defs;
	}


	/**
	 * Extracts the index definitions from the given content.
	 *
	 * @param string $content Content of the file to parse
	 * @return string[] Associative list of index names with index create statements ordered like in the file
	 */
	protected function getIndexDefinitions( string $content ) : array
	{
		$defs = [];
		$matches = [];

		if( preg_match_all( '/CREATE [a-zA-Z]* ?INDEX \"?([a-zA-Z0-9_]+)\"? ON \"?([a-zA-Z0-9_]+)\"? .+([\r\n]{2,4}|$)/sU', $content, $matches, PREG_SET_ORDER ) === false ) {
			throw new \Aimeos\MW\Setup\Exception( 'Unable to get index definitions' );
		}

		foreach( $matches as $match ) {
			$name = $match[2] . '.' . $match[1];
			$defs[$name] = $match[0];
		}

		return $defs;
	}


	/**
	 * Extracts the trigger definitions from the given content.
	 *
	 * @param string $content Content of the file to parse
	 * @return string[] Associative list of trigger names with trigger create statements ordered like in the file
	 */
	protected function getTriggerDefinitions( string $content ) : array
	{
		$defs = [];
		$matches = [];

		$regex = '/CREATE TRIGGER \"?([a-zA-Z0-9_]+)\"? .*([\r\n]{2,4}|$)/sU';
		if( preg_match_all( $regex, $content, $matches, PREG_SET_ORDER ) === false ) {
			throw new \Aimeos\MW\Setup\Exception( 'Unable to get trigger definitions' );
		}

		foreach( $matches as $match ) {
			$defs[$match[1]] = $match[0];
		}

		return $defs;
	}


	/**
	 * Updates the database to get from the source schema to the destination schema
	 *
	 * @param \Doctrine\DBAL\Schema\Schema $src Source schema object
	 * @param \Doctrine\DBAL\Schema\Schema $dest Destination schema object
	 * @param string $rname Resource name of the connection the table belongs to
	 * @return \Aimeos\MW\Setup\Task\Iface Task object for fluent interface
	 */
	protected function update( \Doctrine\DBAL\Schema\Schema $src, \Doctrine\DBAL\Schema\Schema $dest, string $rname )
	{
		$conn = $this->acquire( $rname );

		try
		{
			$dbal = $conn->getRawObject();

			if( !( $dbal instanceof \Doctrine\DBAL\Connection ) ) {
				throw new \Aimeos\MW\Setup\Exception( 'Not a DBAL connection' );
			}

			$platform = $dbal->getDatabasePlatform();

			foreach( $src->getMigrateToSql( $dest, $platform ) as $sql ) {
				$conn->create( $sql )->execute()->finish();
			}
		}
		catch( \Exception $e )
		{
			$this->release( $conn, $rname );
			throw $e;
		}

		$this->release( $conn, $rname );
		return $this;
	}
}
