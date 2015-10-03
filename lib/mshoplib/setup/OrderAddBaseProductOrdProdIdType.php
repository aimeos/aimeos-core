<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds orderprodid and type column to order base product table.
 */
class MW_Setup_Task_OrderAddBaseProductOrdProdIdType extends MW_Setup_Task_Base
{
	private $mysql = array(
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
		return array( 'OrderAddBaseProductProductid' );
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
	 * Adds orderprodid column and/or type column, if it does not exist.
	 *
	 * @param array $stmts Associative array of column names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Add oderprodid and type to order base product', 0 ); $this->status( '' );

		foreach( $stmts as $column => $stmt )
		{
			$table = 'mshop_order_base_product';
			$this->msg( sprintf( 'Checking column "%1$s": ', $column ), 1 );

			if( $this->schema->tableExists( $table )
				&& $this->schema->columnExists( $table, $column ) === false )
			{
				$this->execute( $stmt );
				$this->status( 'added' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
