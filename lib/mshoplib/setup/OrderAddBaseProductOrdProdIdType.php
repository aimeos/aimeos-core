<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds orderprodid and type column to order base product table.
 */
class MW_Setup_Task_OrderAddBaseProductOrdProdIdType extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'ordprodid' => 'ALTER TABLE "mshop_order_base_product" ADD "ordprodid" BIGINT DEFAULT NULL AFTER "siteid"',
		'type' => 'ALTER TABLE "mshop_order_base_product" ADD "type" VARCHAR(32) NOT NULL AFTER "ordprodid"'
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array('OrderAddBaseProductProductid');
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
		$this->_process( $this->_mysql );
	}


	/**
	 * Adds orderprodid column and/or type column, if it does not exist.
	 *
	 * @param array $stmts Associative array of column names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Add oderprodid and type to order base product', 0 ); $this->_status( '' );

		foreach( $stmts as $column => $stmt )
		{
			$table = 'mshop_order_base_product';
			$this->_msg( sprintf( 'Checking column "%1$s": ', $column ), 1 );

			if( $this->_schema->tableExists( $table )
				&& $this->_schema->columnExists( $table, $column ) === false )
			{
				$this->_execute( $stmt );
				$this->_status( 'added' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}
