<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Modifies indexes in the coupon tables.
 */
class CouponModifyIndexes extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'add' => array(
			'mshop_coupon_code' => array(
				'unq_mscouco_sid_code' => 'ALTER TABLE "mshop_coupon_code" ADD UNIQUE INDEX "unq_mscouco_sid_code" ( "siteid", "code" )'
			)
		),

		'delete' => array(
			'mshop_coupon' => array(
				'idx_mscou_stat_start_end' => 'ALTER TABLE "mshop_coupon" DROP INDEX "idx_mscou_stat_start_end"'
			),
			'mshop_coupon_code' => array(
				'idx_mscouco_count_start_end' => 'ALTER TABLE "mshop_coupon_code" DROP INDEX "idx_mscouco_count_start_end"',
				'idx_mscouco_code' => 'ALTER TABLE "mshop_coupon_code" DROP INDEX "idx_mscouco_code"',
				'unq_mscouco_code_siteid' => 'ALTER TABLE "mshop_coupon_code" DROP INDEX "unq_mscouco_code_siteid"'
			),
		)
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'DiscountMoveTablesAndColumesToCoupon' );
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
	 * Modifies indexes in the mshop_coupon table.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( sprintf( 'Modifying indexes in mshop_coupon tables' ), 0 );
		$this->status( '' );

		foreach( $stmts['add'] as $table => $indexes )
		{
			foreach( $indexes as $index => $stmt )
			{
				$this->msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

				if( $this->schema->tableExists( $table ) === true
				&& $this->schema->indexExists( $table, $index ) !== true )
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

		foreach( $stmts['delete'] as $table => $indexes )
		{
			foreach( $indexes as $index => $stmt )
			{
				$this->msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

				if( $this->schema->tableExists( $table ) === true
					&& $this->schema->indexExists( $table, $index ) === true )
				{
					$this->execute( $stmt );
					$this->status( 'dropped' );
				}
				else
				{
					$this->status( 'OK' );
				}
			}
		}

	}

}