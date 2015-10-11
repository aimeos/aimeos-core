<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes collation of code columns for coupon tables.
 */
class OrderCouponColumnCodeCollateToUtf8Bin extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_coupon_code' => 'ALTER TABLE "mshop_coupon_code" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_order_base_coupon' => 'ALTER TABLE "mshop_order_base_coupon" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
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
		return array( 'TablesCreateCoupon' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Changes collation of code columns for coupon tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute
	 */
	protected function process( array $stmts )
	{
		$column = 'code';
		$this->msg( 'Changing coupon code columns', 0 ); $this->status( '' );

		foreach( $stmts as $table=>$stmt )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true
				&& $this->schema->columnExists( $table, $column ) === true
				&& $this->schema->getColumnDetails( $table, $column )->getCollationType() !== 'utf8_bin' )
			{
				$this->execute( $stmt );
				$this->status( 'migrated' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
