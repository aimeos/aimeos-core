<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds name column in order base product attribute table.
 */
class MW_Setup_Task_OrderAddProductAttributeName extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'ALTER TABLE "mshop_order_base_product_attr" ADD "name" VARCHAR(255) NOT NULL AFTER "value"',
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
		return array('TablesCreateMShop');
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_msg( 'Adding name column to order product attribute table', 0 ); $this->_status( '' );

		$this->_process( $this->_mysql );
	}

	/**
	 * Add columns to tables if they doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( $stmts )
	{
		$table = 'mshop_order_base_product_attr';
		$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

		if( $this->_schema->tableExists( $table ) === true
			&& $this->_schema->columnExists( $table, 'name' ) === false )
		{
			$this->_executeList( $stmts );
			$this->_status( 'added' );
		}
		else
		{
			$this->_status( 'OK' );
		}
	}
}