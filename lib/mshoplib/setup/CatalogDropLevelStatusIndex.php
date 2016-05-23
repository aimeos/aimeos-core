<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrate the database schema
 */
class CatalogDropLevelStatusIndex extends \Aimeos\MW\Setup\Task\Base
{
	private $list = array(
		'mysql' => 'DROP INDEX "idx_mscat_level_status" ON "mshop_catalog"',
		'pgsql' => 'DROP INDEX "idx_mscat_level_status"',
	);


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
		$this->clean();
	}


	/**
	 * Clean up database schema
	 */
	public function clean()
	{
		$this->msg( 'Dropping index "idx_mscat_level_status"', 0 );

		$schema = $this->getSchema( 'db-catalog' );

		if( isset( $this->list[$schema->getName()] )
			&& $schema->tableExists( 'mshop_catalog' ) === true
			&& $schema->indexExists( 'mshop_catalog', 'idx_mscat_level_status' ) === true )
		{
			$this->execute( $this->list[$schema->getName()] );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
