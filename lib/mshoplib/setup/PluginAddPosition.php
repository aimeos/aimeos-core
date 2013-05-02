<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
* Adds position column to plugin table.
*/

class MW_Setup_Task_PluginAddPosition extends MW_Setup_Task_Abstract
{
	private $_mysql = 'ALTER TABLE "mshop_plugin" ADD "pos" INTEGER NOT NULL AFTER "config"';


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
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmt SQL statement to execute for adding columns
	 */
	protected function _process( $stmt )
	{
		$this->_msg( 'Adding position column to mshop_plugin table', 0 );

		if( $this->_schema->tableExists( 'mshop_plugin' ) === true
			&& $this->_schema->columnExists( 'mshop_plugin', 'pos' ) === false )
		{
			$this->_execute( $stmt );
			$this->_status( 'added' );
		}
		else
		{
			$this->_status( 'OK' );
		}
	}

}