<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Modifies indexes in the coupon tables.
 */
class MW_Setup_Task_CouponModifyIndexes extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'add' => array (
			'mshop_coupon_code' => array (
				'unq_mscouco_sid_code' => 'ALTER TABLE "mshop_coupon_code" ADD UNIQUE INDEX "unq_mscouco_sid_code" ( "siteid", "code" )'
			)
		),

		'delete' => array (
			'mshop_coupon' => array (
				'idx_mscou_stat_start_end' => 'ALTER TABLE "mshop_coupon" DROP INDEX "idx_mscou_stat_start_end"'
			),
			'mshop_coupon_code' => array (
				'idx_mscouco_count_start_end' => 'ALTER TABLE "mshop_coupon_code" DROP INDEX "idx_mscouco_count_start_end"',
				'idx_mscouco_code' => 'ALTER TABLE "mshop_coupon_code" DROP INDEX "idx_mscouco_code"',
				'unq_mscouco_code_siteid' => 'ALTER TABLE "mshop_coupon_code" DROP INDEX "unq_mscouco_code_siteid"'
			),
		)
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'DiscountMoveTablesAndColumesToCoupon' );
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
	 * Modifies indexes in the mshop_coupon table.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( sprintf( 'Modifying indexes in mshop_coupon tables' ), 0 );
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