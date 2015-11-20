<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Modifies indexes in the product tables.
 */
class MW_Setup_Task_ProductModifyIndexes extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'add' => array(
			'mshop_product_list' => array(
				'fk_msproli_pid' => 'ALTER TABLE "mshop_product_list" ADD INDEX "fk_msproli_pid" ("parentid")',
			),
			'mshop_product_stock_warehouse' => array(
				'unq_msprostwa_sid_code' => 'ALTER TABLE "mshop_product_stock_warehouse" ADD UNIQUE INDEX "unq_msprostwa_sid_code" ("siteid", "code")'
			),
			'mshop_product_stock' => array(
				// 'fk_msprost_prodid' => 'ALTER TABLE "mshop_product_stock" ADD INDEX "fk_msprost_prodid" ("prodid")',
				'unq_msprost_sid_pid_wid' => 'ALTER TABLE "mshop_product_stock" ADD UNIQUE INDEX "unq_msprost_sid_pid_wid" ("siteid", "prodid", "warehouseid")'
			),
			'mshop_product_site' => array(
				'fk_msprosi_parentid' => 'ALTER TABLE "mshop_product_site" ADD INDEX "fk_msprosi_parentid" ("parentid")',
				'unq_msprosi_sid_pid' => 'ALTER TABLE "mshop_product_site" ADD UNIQUE INDEX "unq_msprosi_sid_pid" ("siteid", "parentid")',
			)
		),
		'delete' => array(
			'mshop_product' => array(
				'idx_mspro_sid_start_end_stat' => 'ALTER TABLE "mshop_product" DROP INDEX "idx_mspro_sid_start_end_stat"'
			),
			'mshop_product_list' => array(
				'fk_msproli_parentid' => 'ALTER TABLE "mshop_product_list" DROP INDEX "fk_msproli_parentid"',
				'unq_msproli_pid_sid_tid_rid_dm' => 'ALTER TABLE "mshop_product_list" DROP INDEX "unq_msproli_pid_sid_tid_rid_dm"',
			),
			'mshop_product_stock_warehouse' => array(
				'unq_msprostwa_code_sid' => 'ALTER TABLE "mshop_product_stock_warehouse" DROP INDEX "unq_msprostwa_code_sid"'
			),
			'mshop_product_stock' => array(
				'unq_msprost_pid_sid_wid' => 'ALTER TABLE "mshop_product_stock" DROP INDEX "unq_msprost_pid_sid_wid"'
			),
			'mshop_product_site' => array(
				'unq_msprosi_pid_sid' => 'ALTER TABLE "mshop_product_site" DROP INDEX "unq_msprosi_pid_sid"'
			)
		)
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductRenameListConstraint' );
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
	 * Adds and modifies indexes in the mshop_product table.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( sprintf( 'Modifying indexes in mshop_product tables' ), 0 );
		$this->_status( '' );

		foreach( $stmts['add'] as $table => $indexes )
		{
			foreach( $indexes as $index => $stmt )
			{
				$this->_msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

				if( $this->_schema->tableExists( $table ) === true
					&& $this->_schema->indexExists( $table, $index ) !== true )
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

		foreach( $stmts['delete'] as $table => $indexes )
		{
			foreach( $indexes as $index => $stmt )
			{
				$this->_msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

				if( $this->_schema->tableExists( $table ) === true
					&& $this->_schema->indexExists( $table, $index ) === true )
				{
					$this->_execute( $stmt );
					$this->_status( 'dropped' );
				}
				else
				{
					$this->_status( 'OK' );
				}
			}
		}
	}

}