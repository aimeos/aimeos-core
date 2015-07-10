<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Creates all required columns
 */
class MW_Setup_Task_CouponAddMtimeCtimeEditor extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_coupon' => array (
			'mtime' => array(
				'ALTER TABLE "mshop_coupon" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_coupon" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_coupon" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_coupon" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_coupon" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),
		'mshop_coupon_code' => array (
			'mtime' => array(
				'ALTER TABLE "mshop_coupon_code" ADD "mtime" DATETIME NOT NULL AFTER "end"',
				'UPDATE "mshop_coupon_code" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_coupon_code" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_coupon_code" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_coupon_code" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_order_base_coupon' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_order_base_coupon" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_order_base_coupon" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_order_base_coupon" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'DiscountMoveTablesAndColumesToCoupon', 'OrderRenameTables' );
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
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Adding mtime, ctime, editor columns to coupon tables', 0 );
		$this->_status( '' );

		foreach( $stmts as $table => $columns )
		{
			$this->_msg( sprintf( 'Checking table "%1$s"', $table ), 1 );
			$this->_status( '' );

			if( $this->_schema->tableExists( $table ) === true )
			{
				foreach ( $columns as $column => $stmtList )
				{
					$this->_msg( sprintf( 'Checking column "%1$s": ', $column ), 2 );

					if( $this->_schema->columnExists( $table, $column ) === false )
					{
						$this->_executeList( $stmtList );
						$this->_status( 'added' );
					} else {
						$this->_status( 'OK' );
					}
				}
			}
		}
	}
}