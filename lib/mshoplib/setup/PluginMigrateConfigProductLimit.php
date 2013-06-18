<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
* Migrates product limit plugin configuration.
*/

class MW_Setup_Task_PluginMigrateConfigProductLimit extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'select' => 'SELECT COUNT(*) AS "cnt" FROM "mshop_plugin" WHERE "config" LIKE \'%"limit"%\'',
		'update' => 'UPDATE "mshop_plugin" SET "config" = REPLACE("config", \'"limit"\', \'"single-number-max"\') WHERE "config" LIKE \'%"limit"%\'',
	);

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
		return array();
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
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Migrating configuration of "ProductLimit" plugin', 0 );

		if( $this->_schema->columnExists( 'mshop_plugin', 'config' ) === true )
		{
			if( $this->_getValue( $stmts['select'], 'cnt' ) > 0 )
			{
				$this->_execute( $stmts['update'] );
				$this->_status( 'migrated' );
				return;
			}
		}

		$this->_status( 'OK' );
	}

}