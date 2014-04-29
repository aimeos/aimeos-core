<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Creates all required columns
 */
class MW_Setup_Task_CouponMigrateConfigKeys extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'UPDATE "mshop_coupon" SET "provider" = CONCAT("provider", \',Required\')
			WHERE "provider" NOT LIKE \'%,Required%\' AND "config" LIKE \'%reqproduct%\'',
		'UPDATE "mshop_coupon" SET "config" = REPLACE("config", \'reqproduct\', \'required.productcode\')
			WHERE "config" LIKE \'%reqproduct%\'',

		'UPDATE "mshop_coupon" SET "provider" = CONCAT("provider", \',BasketValues\')
			WHERE "provider" NOT LIKE \'%,BasketValues%\' AND "config" LIKE \'%minorder%\'',
		'UPDATE "mshop_coupon" SET "config" = REPLACE("config", \'minorder\', \'basketvalues.total-value-min\')
			WHERE "config" LIKE \'%minorder%\'',

		'UPDATE "mshop_coupon" SET "config" = REPLACE("config", \'"product"\', \'"fixedrebate.productcode"\')
			WHERE "provider" LIKE \'%FixedRebate%\' AND "config" LIKE \'%"product"%\'',
		'UPDATE "mshop_coupon" SET "config" = REPLACE("config", \'"rebate"\', \'"fixedrebate.rebate"\')
			WHERE "provider" LIKE \'%FixedRebate%\' AND "config" LIKE \'%"rebate"%\'',

		'UPDATE "mshop_coupon" SET "config" = REPLACE("config", \'"product"\', \'"freeshipping.productcode"\')
			WHERE "provider" LIKE \'%FreeShipping%\' AND "config" LIKE \'%"product"%\'',

		'UPDATE "mshop_coupon" SET "config" = REPLACE("config", \'"product"\', \'"percentrebate.productcode"\')
			WHERE "provider" LIKE \'%PercentRebate%\' AND "config" LIKE \'%"product"%\'',
		'UPDATE "mshop_coupon" SET "config" = REPLACE("config", \'"rebate"\', \'"percentrebate.rebate"\')
			WHERE "provider" LIKE \'%PercentRebate%\' AND "config" LIKE \'%"rebate"%\'',

		'UPDATE "mshop_coupon" SET "config" = REPLACE("config", \'"product"\', \'"present.productcode"\')
			WHERE "provider" LIKE \'%Present%\' AND "config" LIKE \'%"product"%\'',
		'UPDATE "mshop_coupon" SET "config" = REPLACE("config", \'"quantity"\', \'"present.quantity"\')
			WHERE "provider" LIKE \'%Present%\' AND "config" LIKE \'%"quantity"%\'',
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
	 * Migrates the configuration keys of the coupon providers.
	 *
	 * @param array $stmts List of SQL statements to execute for migrating the configuration
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Migrating configuration keys in coupon tables', 0 );

		$this->_executeList( $stmts );
		$this->_status( 'done' );
	}
}