<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds warehouse column to order base product tables.
 */
class MW_Setup_Task_OrderAddBaseProductWarehouse extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'ALTER TABLE "mshop_order_base_product" ADD "warehousecode" VARCHAR(32) NOT NULL COLLATE utf8_bin AFTER "suppliercode"',
		'UPDATE "mshop_order_base_product" SET "warehousecode" = \'default\' WHERE "warehousecode" = \'\'',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables' );
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
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Adding warehouse code to order base product table', 0 );

		$table = 'mshop_order_base_product';
		$schema = $this->_getSchema( 'db-order' );

		if ( $schema->tableExists( $table ) === true &&
			$schema->columnExists( $table, 'warehousecode' ) === false )
		{
			$this->_executeList( $stmts, 'db-order' );
			$this->_status( 'done' );
		} else {
			$this->_status( 'OK' );
		}
	}
}