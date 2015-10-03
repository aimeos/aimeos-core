<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Setup
 */


/**
 * Common manager class for setup and upgrade processes.
 *
 * @package MW
 * @subpackage Setup
 */
abstract class MW_Setup_Manager_Base implements MW_Setup_Manager_Iface
{
	/**
	 * Creates a new database schema object.
	 *
	 * @param MW_DB_Connection_Iface $conn Database connection object
	 * @param string $adapter Database adapter, e.g. "mysql", "pgsql", etc.
	 * @param string $dbname Name of the database that will be used
	 * @return MW_Setup_DBSchema_Iface Database schema object
	 */
	protected  function createSchema( MW_DB_Connection_Iface $conn, $adapter, $dbname )
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
	protected function includeFile( $pathname )
	{
		if( ( include_once $pathname ) === false ) {
			throw new MW_Setup_Exception( sprintf( 'Unable to include file "%1$s"', $pathname ) );
		}
	}


	/**
	 * Creates the tasks from the given directories.
	 *
	 * @param array $paths List of paths containing setup task classes
	 * @param MW_Setup_DBSchema_Iface $schema Database schema object
	 * @param MW_DB_Connection_Iface $conn Database connection object
	 * @param mixed $additional Additional data that should be handed over to the setup tasks
	 * @return MW_Setup_Task_Iface[] List of setup task objects
	 */
	protected function createTasks( array $paths, MW_Setup_DBSchema_Iface $schema, MW_DB_Connection_Iface $conn, $additional )
	{
		$tasks = array();

		foreach( $paths as $path )
		{
			foreach( new DirectoryIterator( $path ) as $item )
			{
				if( $item->isDir() === true || substr( $item->getFilename(), -4 ) != '.php' ) { continue; }

				$this->includeFile( $item->getPathName() );

				$taskname = substr( $item->getFilename(), 0, -4 );
				$classname = 'MW_Setup_Task_' . $taskname;

				if( class_exists( $classname ) === false ) {
					throw new MW_Setup_Exception( sprintf( 'Class "%1$s" not found', $classname ) );
				}

				$interface = 'MW_Setup_Task_Iface';
				$task = new $classname( $schema, $conn, $additional );

				if( ( $task instanceof $interface ) === false ) {
					throw new MW_Setup_Exception( sprintf( 'Class "%1$s" doesn\'t implement "%2$s"', $classname, 'MW_Setup_Task_Iface' ) );
				}

				$tasks[$taskname] = $task;
			}
		}

		return $tasks;
	}
}
