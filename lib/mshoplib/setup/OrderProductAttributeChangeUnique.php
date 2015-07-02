<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


/**
 * Adds type column to unique index in order base product attribute table.
 */
class MW_Setup_Task_OrderProductAttributeChangeUnique extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'CREATE UNIQUE INDEX "unq_msordbaprat_opid_type_code" ON "mshop_order_base_product_attr" ("ordprodid","type","code")',
		'DROP INDEX "unq_msordbaprat_ordprodid_code" ON "mshop_order_base_product_attr"',
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables' );
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
	 * Migrates service attribute data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$table = 'mshop_order_base_product_attr';
		$this->_msg( sprintf( 'Adding type to unique index to "%1$s"', $table ), 0 );

		if( $this->_schema->tableExists( $table ) === true
			&& $this->_schema->constraintExists( $table, 'unq_msordbaprat_ordprodid_code' ) === true )
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
