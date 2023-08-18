<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 */


namespace Aimeos\Upscheme\Task;


class OrderMigrateServiceAttributeTypes extends Base
{
	public function after() : array
	{
		return ['Order', 'OrderRenameTables'];
	}


	public function up()
	{
		$this->info( 'Migrating attribute types of order services', 'vv' );

		$conn = $this->context()->db( 'db-order' );

		$conn->create( 'UPDATE "mshop_order_service_attr" SET "type" = \'\' WHERE "type" = \'payment\'' )->execute()->finish();
		$conn->create( 'UPDATE "mshop_order_service_attr" SET "type" = \'hidden\' WHERE "type" = \'payment/hidden\'' )->execute()->finish();
		$conn->create( 'UPDATE "mshop_order_service_attr" SET "type" = \'tx\' WHERE "type" = \'payment/paypal\'' )->execute()->finish();
		$conn->create( 'UPDATE "mshop_order_service_attr" SET "type" = \'paypal/txn\' WHERE "type" = \'payment/paypal/txn\'' )->execute()->finish();
	}
}
