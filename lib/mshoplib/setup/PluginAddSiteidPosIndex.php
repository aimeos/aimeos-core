<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrate the database schema
 */
class PluginAddSiteidPosIndex extends \Aimeos\MW\Setup\Task\Base
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
		$this->clean();
	}


	/**
	 * Clean up database schema
	 */
	public function clean()
	{
		$this->msg( 'Adding index "idx_msplu_sid_pos"', 0 );

		$schema = $this->getSchema( 'db-plugin' );

		if( $schema->tableExists( 'mshop_plugin' ) === true
			&& $schema->indexExists( 'mshop_plugin', 'idx_msplu_sid_pos' ) === false )
		{
			$this->execute( 'CREATE INDEX "idx_msplu_sid_pos" ON "mshop_plugin" ("siteid", "pos")' );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
