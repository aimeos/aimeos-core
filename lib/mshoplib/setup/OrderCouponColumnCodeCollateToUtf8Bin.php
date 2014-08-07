<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Changes collation of code columns for coupon tables.
 */
class MW_Setup_Task_OrderCouponColumnCodeCollateToUtf8Bin extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
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
		return array('TablesCreateCoupon');
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Changes collation of code columns for coupon tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute
	 */
	protected function _process( array $stmts )
	{
		$column = 'code';
		$this->_msg( 'Changing coupon code columns', 0 ); $this->_status( '' );

		foreach( $stmts as $table=>$stmt )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true
				&& $this->_schema->columnExists( $table, $column ) === true
				&& $this->_schema->getColumnDetails( $table, $column )->getCollationType() !== 'utf8_bin')
			{
				$this->_execute( $stmt );
				$this->_status( 'migrated' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}
