<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrate the database schema
 */
class CatalogAddStatusLevelIndex extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array();
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Update database schema
	 */
	public function migrate()
	{
		$this->msg( 'Adding index to "idx_mscat_status_level"', 0 );

		$schema = $this->getSchema( 'db-catalog' );

		if( $schema->tableExists( 'mshop_catalog' ) === true
			&& $schema->indexExists( 'mshop_catalog', 'idx_mscat_status_level' ) === false )
		{
			$this->execute( 'CREATE INDEX "idx_mscat_status_level" ON "mshop_catalog" ("status", "level")' );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
