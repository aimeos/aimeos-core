<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: ProductStockExtendUniqueByWarehouseid.php 14456 2011-12-19 16:18:24Z fblasel $
 */


/**
 * Changes the unique constraint for table mshop_product_stock.
 */
class MW_Setup_Task_ProductStockExtendUniqueByWarehouseid extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
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
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array('TablesCreateMShop');
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Changes UNIQUE constraint for customer if necessary.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$constraint = 'unq_msprost_pid_sid';
		$table = 'mshop_product_stock';
		
		$this->_msg( 'Changing product stock unique constraint', 0 );

		if ( $this->_schema->tableExists( $table ) && $this->_schema->constraintExists( $table, $constraint ) )
		{
			$this->_executeList( $stmts );
			$this->_status( 'changed' );
		} else {
			$this->_status( 'OK' );
		}
	}
}
