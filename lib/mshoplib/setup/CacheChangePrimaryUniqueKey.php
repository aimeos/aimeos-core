<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


/**
 * Changes primary to unique key in madmin_cache
 */
class MW_Setup_Task_CacheChangePrimaryUniqueKey extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'ALTER TABLE "madmin_cache" DROP PRIMARY KEY, ADD UNIQUE "unq_macac_id_siteid" ("id", "siteid")',
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
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMadmin' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}

	/**
	 * Add column to table if it doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( $stmts )
	{
		$this->_msg( 'Changing index type in madmin_cache', 0 );

		if( $this->_schema->tableExists( 'madmin_cache' ) === true
			&& $this->_schema->constraintExists( 'madmin_cache', 'PRIMARY' ) === true )
		{
			$this->_executeList( $stmts );
			$this->_status( 'done' );
		}
		else
		{
			$this->_status( 'OK' );
		}
	}
}
