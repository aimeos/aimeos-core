<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds code column to supplier table.
 */
class SupplierAddCode extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_supplier' => 'ALTER TABLE "mshop_supplier" ADD "code" VARCHAR(32) NOT NULL DEFAULT \'\' AFTER "siteid"',
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
	 * Add column to table if it doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( $stmts )
	{
		$this->msg( 'Adding code column to mshop_supplier', 0 );

		if( $this->schema->tableExists( 'mshop_supplier' ) === true
			&& $this->schema->columnExists( 'mshop_supplier', 'code' ) === false )
		{
			$this->executeList( $stmts );
			$this->status( 'added' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}