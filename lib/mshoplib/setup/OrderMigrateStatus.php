<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrate supplier code to ID and name in order product table
 */
class OrderMigrateStatus extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Migrate database schema
	 */
	public function migrate()
	{
		$this->msg( 'Migrate order delivery/payment status', 0 );

		$schema = $this->getSchema( 'db-order' );
		$conn = $this->acquire( 'db-order' );

		if( $schema->tableExists( 'mshop_order' ) )
		{
			$conn->create( 'UPDATE "mshop_order" SET "statuspayment" = -1 WHERE "statuspayment" IS NULL' )->execute()->finish();
			$conn->create( 'UPDATE "mshop_order" SET "statusdelivery" = -1 WHERE "statusdelivery" IS NULL' )->execute()->finish();
		}

		if( $schema->tableExists( 'mshop_order_base_product' ) )
		{
			$conn->create( 'UPDATE "mshop_order_base_product" SET "status" = -1 WHERE "status" IS NULL' )->execute()->finish();
			$conn->create( 'UPDATE "mshop_order_base_product" SET "statuspayment" = -1 WHERE "statuspayment" IS NULL' )->execute()->finish();
		}

		$this->release( $conn, 'db-order' );

		$this->status( 'OK' );
	}
}
