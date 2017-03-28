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
 * Common manager class for setup and upgrade processes.
 *
 * @package MW
 * @subpackage Setup
 */
abstract class Base implements \Aimeos\MW\Setup\Manager\Iface
{
	private static $taskPaths = [];


	/**
	 * Initializes the object and sets up the autoloader
	 *
	 * @param array $taskPaths List of directories containing the setup tasks
	 */
	public function __construct( array $taskPaths )
	{
		self::$taskPaths = $taskPaths;

		if( spl_autoload_register( 'Aimeos\MW\Setup\Manager\Base::autoload' ) === false ) {
			throw new \Aimeos\MW\Setup\Exception( 'Unable to register Aimeos\MW\Setup\Manager\Base::autoload' );
		}
	}


	/**
	 * Autoloader for setup tasks.
	 *
	 * @param string $classname Name of the class to load
	 * @return boolean True if class was found, false if not
	 */
	public static function autoload( $classname )
	{
		if( strncmp( $classname, 'Aimeos\\MW\\Setup\\Task\\', 21 ) === 0 )
		{
			$fileName = substr( $classname, 21 ) . '.php';

			foreach( self::$taskPaths as $path )
			{
				$file = $path . '/' . $fileName;

				if( file_exists( $file ) === true && ( include_once $file ) !== false ) {
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * Creates a new database schema object.
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection object
	 * @param string $adapter Database adapter, e.g. "mysql", "pgsql", etc.
	 * @param string $dbname Name of the database that will be used
	 * @return \Aimeos\MW\Setup\DBSchema\Iface Database schema object
	 */
	protected  function createSchema( \Aimeos\MW\DB\Connection\Iface $conn, $adapter, $dbname )
	{
		if( empty( $adapter ) || ctype_alnum( $adapter ) === false ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Invalid database adapter "%1$s"', $adapter ) );
		}

		$classname = '\\Aimeos\\MW\\Setup\\DBSchema\\' . ucwords( strtolower( $adapter ) );

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Database schema class "%1$s" not found', $classname ) );
		}

		return new $classname( $conn, $dbname, $adapter );
	}


	/**
	 * Includes a PHP file.
	 *
	 * @param string $pathname Path to the file including the file name
	 */
	protected function includeFile( $pathname )
	{
		if( ( include_once $pathname ) === false ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Unable to include file "%1$s"', $pathname ) );
		}
	}


	/**
	 * Creates the tasks from the given directories.
	 *
	 * @param array $paths List of paths containing setup task classes
	 * @param \Aimeos\MW\Setup\DBSchema\Iface $schema Database schema object
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection object
	 * @param mixed $additional Additional data that should be handed over to the setup tasks
	 * @return \Aimeos\MW\Setup\Task\Iface[] List of setup task objects
	 */
	protected function createTasks( array $paths, \Aimeos\MW\Setup\DBSchema\Iface $schema, \Aimeos\MW\DB\Connection\Iface $conn, $additional )
	{
		$tasks = [];

		foreach( $paths as $path )
		{
			foreach( new \DirectoryIterator( $path ) as $item )
			{
				if( $item->isDir() === true || substr( $item->getFilename(), -4 ) != '.php' ) { continue; }

				$this->includeFile( $item->getPathName() );

				$taskname = substr( $item->getFilename(), 0, -4 );
				$classname = '\\Aimeos\\MW\\Setup\\Task\\' . $taskname;

				if( class_exists( $classname ) === false ) {
					throw new \Aimeos\MW\Setup\Exception( sprintf( 'Class "%1$s" not found', $classname ) );
				}

				$interface = '\\Aimeos\\MW\\Setup\\Task\\Iface';
				$task = new $classname( $schema, $conn, $additional, $paths );

				if( ( $task instanceof $interface ) === false ) {
					throw new \Aimeos\MW\Setup\Exception( sprintf( 'Class "%1$s" doesn\'t implement "%2$s"', $classname, '\\Aimeos\\MW\\Setup\\Task\\Iface' ) );
				}

				$tasks[$taskname] = $task;
			}
		}

		return $tasks;
	}
}
