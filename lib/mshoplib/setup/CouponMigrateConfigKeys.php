<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Creates all required columns
 */
class CouponMigrateConfigKeys extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
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
	 * Migrates the configuration keys of the coupon providers.
	 *
	 * @param array $stmts List of SQL statements to execute for migrating the configuration
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Migrating configuration keys in coupon tables', 0 );

		if( $this->getSchema( 'db-coupon' )->tableExists( 'mshop_coupon' ) === true )
		{
			$this->executeList( $stmts, 'db-coupon' );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}

	}
}