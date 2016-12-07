<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Creates all required columns
 */
class CouponAddMtimeCtimeEditor extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_coupon' => array(
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
		'mshop_coupon_code' => array(
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
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Adding mtime, ctime, editor columns to coupon tables', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $columns )
		{
			$this->msg( sprintf( 'Checking table "%1$s"', $table ), 1 );
			$this->status( '' );

			if( $this->schema->tableExists( $table ) === true )
			{
				foreach( $columns as $column => $stmtList )
				{
					$this->msg( sprintf( 'Checking column "%1$s": ', $column ), 2 );

					if( $this->schema->columnExists( $table, $column ) === false )
					{
						$this->executeList( $stmtList );
						$this->status( 'added' );
					} else {
						$this->status( 'OK' );
					}
				}
			}
		}
	}
}