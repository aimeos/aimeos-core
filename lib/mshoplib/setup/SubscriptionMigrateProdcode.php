<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Updates product code in subscriptions
 */
class SubscriptionMigrateProdcode extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Migrate database schema
	 */
	public function migrate()
	{
		$dbdomain = 'db-order';
		$this->msg( 'Updating product code in subscriptions', 0 );

		if( $this->getSchema( $dbdomain )->tableExists( 'mshop_subscription' ) === false )
		{
			$this->status( 'OK' );
			return;
		}

		$start = 0;
		$conn = $this->acquire( $dbdomain );
		$update = '
			UPDATE "mshop_subscription"
			SET "mshop_subscription"."prodcode" = (
				SELECT "mshop_order_base_product"."prodcode"
				FROM "mshop_order_base_product"
				WHERE "mshop_subscription"."ordprodid" = "mshop_order_base_product"."id"
				LIMIT 1
			) WHERE "mshop_subscription"."prodcode" = \'\'
		';

		$conn->create( $update )->execute()->finish();
		$this->release( $conn, $dbdomain );

		$this->status( 'done' );
	}
}
