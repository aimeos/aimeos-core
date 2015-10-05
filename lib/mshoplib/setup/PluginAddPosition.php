<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds position column to plugin table.
 */
class PluginAddPosition extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = 'ALTER TABLE "mshop_plugin" ADD "pos" INTEGER NOT NULL AFTER "config"';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array();
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Add column to table if the column doesn't exist.
	 *
	 * @param string $stmt SQL statement to execute for adding columns
	 */
	protected function process( $stmt )
	{
		$this->msg( 'Adding position column to mshop_plugin table', 0 );

		if( $this->schema->tableExists( 'mshop_plugin' ) === true
			&& $this->schema->columnExists( 'mshop_plugin', 'pos' ) === false )
		{
			$this->execute( $stmt );
			$this->status( 'added' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}

}