<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Modifies indexes in the order_base_coupon tables.
 */
class MW_Setup_Task_OrderBaseCouponModifyIndexes extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'add' => array (
			'mshop_order_base_coupon' => array (
				'fk_msordbaco_baseid' => 'ALTER TABLE "mshop_order_base_coupon" ADD INDEX "fk_msordbaco_baseid" ("baseid")',
			)
		),
		'delete' => array (
			'mshop_order_base_coupon' => array (
				'idx_msordbaco_bid_code_sid' => 'ALTER TABLE "mshop_order_base_coupon" DROP INDEX "idx_msordbaco_bid_code_sid"'
			)
		)
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
		return array( 'TablesCreateCoupon' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Modifies indexes in the mshop_order_base_coupon table.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( sprintf( 'Modifying indexes in mshop_order_base_coupon tables' ), 0 );
		$this->_status('');

		foreach( $stmts['add'] AS $table => $indexes )
		{
			foreach ( $indexes AS $index => $stmt )
			{
				$this->_msg(sprintf('Checking index "%1$s": ', $index), 1);

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

		foreach( $stmts['delete'] AS $table => $indexes )
		{
			foreach ( $indexes AS $index => $stmt )
			{
				$this->_msg(sprintf('Checking index "%1$s": ', $index), 1);

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