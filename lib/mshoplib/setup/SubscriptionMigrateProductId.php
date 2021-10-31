<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\Upscheme\Task;


class SubscriptionMigrateProductId extends Base
{
	public function after() : array
	{
		return ['Subscription'];
	}


	public function up()
	{
		$this->info( 'Updating product ID in subscriptions', 'v' );

		$this->db( 'db-order' )->exec( '
			UPDATE mshop_subscription
			SET productid = (
				SELECT obp.prodid
				FROM mshop_order_base_product AS obp
				WHERE mshop_subscription.ordprodid = obp.id
			) WHERE productid = \'\'
		' );
	}
}
