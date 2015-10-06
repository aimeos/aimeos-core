<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Changes the unique constraint for table mshop_product_stock.
 */
class MW_Setup_Task_ProductStockExtendUniqueByWarehouseid extends MW_Setup_Task_Abstract
{
	private $mysql = array(
		'ALTER TABLE "mshop_product_stock"
			DROP INDEX "unq_msprost_pid_sid",
			ADD UNIQUE "unq_msprost_pid_sid_wid" ("prodid", "siteid", "warehouseid")'
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
	 * Changes UNIQUE constraint for customer if necessary.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$constraint = 'unq_msprost_pid_sid';
		$table = 'mshop_product_stock';
		
		$this->msg( 'Changing product stock unique constraint', 0 );

		if( $this->schema->tableExists( $table ) && $this->schema->constraintExists( $table, $constraint ) )
		{
			$this->executeList( $stmts );
			$this->status( 'changed' );
		} else {
			$this->status( 'OK' );
		}
	}
}
