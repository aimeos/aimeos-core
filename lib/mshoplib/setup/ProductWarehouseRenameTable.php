<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Moves product warehouse table to product stock warehouse.
 */
class MW_Setup_Task_ProductWarehouseRenameTable extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'RENAME TABLE "mshop_product_warehouse" TO "mshop_product_stock_warehouse"',
		'pk_msprowa_id' => 'ALTER TABLE "mshop_product_stock_warehouse" DROP PRIMARY KEY, ADD CONSTRAINT "pk_msprostwa_id" PRIMARY KEY ("id")',
		'unq_msprowa_code_sid' => 'ALTER TABLE "mshop_product_stock_warehouse" DROP INDEX "unq_msprowa_code_sid", ADD CONSTRAINT "unq_msprostwa_code_sid" UNIQUE ("code", "siteid")',
		'fk_msprowa_siteid' => 'ALTER TABLE "mshop_product_stock_warehouse" DROP FOREIGN KEY "fk_msprowa_siteid", ADD CONSTRAINT "fk_msprostwa_siteid" FOREIGN KEY ("siteid") REFERENCES "mshop_locale_site" ("id") ON UPDATE CASCADE ON DELETE CASCADE',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ColumnCodeCollateToUtf8Bin' );
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
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Renaming product warehouse table', 0 );

		if( $this->_schema->tableExists( 'mshop_product_warehouse' ) === true )
		{
			$this->_executeList( $stmts );
			$this->_status( 'renamed' );
		}
		else
		{
			$this->_status( 'OK' );
		}
	}
}
