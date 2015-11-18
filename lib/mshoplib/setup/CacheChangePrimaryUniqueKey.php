<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes primary to unique key in madmin_cache
 */
class CacheChangePrimaryUniqueKey extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
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
	protected function mysql()
	{
		$this->process( $this->mysql );
	}

	/**
	 * Add column to table if it doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( $stmts )
	{
		$this->msg( 'Changing index type in madmin_cache', 0 );

		if( $this->schema->tableExists( 'madmin_cache' ) === true
			&& $this->schema->constraintExists( 'madmin_cache', 'PRIMARY KEY' ) === false )
		{
			$this->executeList( $stmts );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}