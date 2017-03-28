<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Creates all platform specific tables
 */
class TablesCreatePlatform extends TablesCreateMShop
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMAdmin', 'TablesCreateMShop' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Removes old columns and sequences
	 */
	public function clean()
	{
	}


	/**
	 * Creates the platform specific schema
	 */
	public function migrate()
	{
		$this->msg( 'Creating platform specific schema', 0 );
		$this->status( '' );

		$ds = DIRECTORY_SEPARATOR;

		$this->setupPlatform( 'db-index', 'mysql', realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'index-mysql.sql' );
		$this->setupPlatform( 'db-order', 'mysql', realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'order-mysql.sql' );
		$this->setupPlatform( 'db-text', 'mysql', realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'text-mysql.sql' );

		$this->setupPlatform( 'db-index', 'pgsql', realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'index-pgsql.sql' );
		$this->setupPlatform( 'db-order', 'pgsql', realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'order-pgsql.sql' );
		$this->setupPlatform( 'db-text', 'pgsql', realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'text-pgsql.sql' );
	}


	/**
	 * Creates all required tables if they doesn't exist
	 */
	protected function setupPlatform( $rname, $adapter, $filepath )
	{
		$schema = $this->getSchema( $rname );

		if( $adapter !== $schema->getName() ) {
			return;
		}

		$this->setup( array( $rname => $filepath ) );
	}
}
