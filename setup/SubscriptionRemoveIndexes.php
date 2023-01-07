<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class SubscriptionRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Subscription'];
	}


	public function up()
	{
		$this->info( 'Remove subscription indexes with siteid column first', 'vv' );

		$this->db( 'db-subscription' )
			->dropIndex( 'mshop_subscription', 'idx_mssub_sid_next_stat' )
			->dropIndex( 'mshop_subscription', 'idx_mssub_sid_baseid' )
			->dropIndex( 'mshop_subscription', 'idx_mssub_sid_opid' )
			->dropIndex( 'mshop_subscription', 'idx_mssub_sid_pid_period' );
	}
}
