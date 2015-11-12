<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Renames the "couponid" column to "parentid"
 */
class CouponChangeCodeCouponidParentid extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'couponid' => array(
			'ALTER TABLE "mshop_coupon_code" DROP FOREIGN KEY "fk_mscouco_couponid"',
			'ALTER TABLE "mshop_coupon_code" CHANGE "couponid" "parentid" INTEGER NOT NULL',
			'ALTER TABLE "mshop_coupon_code" ADD CONSTRAINT "fk_mscouco_parentid" FOREIGN KEY ("parentid") REFERENCES "mshop_coupon" ("id") ON UPDATE CASCADE ON DELETE CASCADE',
		),
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
	 * Changes the column in table
	 *
	 * array string $stmts List of SQL statements for changing the columns
	 */
	protected function process( array $stmts )
	{
		$table = 'mshop_coupon_code';
		$this->msg( sprintf( 'Rename "couponid" to "parentid" in table "%1$s"', $table ), 0 ); $this->status( '' );

		foreach( $stmts as $column => $stmts )
		{
			$this->msg( sprintf( 'Checking column "%1$s"', $column ), 1 );

			if( $this->schema->tableExists( $table )
				&& $this->schema->columnExists( $table, $column ) === true
			) {
				$this->executeList( $stmts );
				$this->status( 'done' );
			} else {
				$this->status( 'OK' );
			}
		}
	}
}
