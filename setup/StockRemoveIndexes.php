<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class StockRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Stock'];
	}


	public function up()
	{
		$this->info( 'Remove stock indexes with siteid column first', 'vv' );

		$this->db( 'db-stock' )
			->dropIndex( 'mshop_stock', 'unq_mssto_sid_pid_ty' )
			->dropIndex( 'mshop_stock', 'idx_mssto_sid_stocklevel' )
			->dropIndex( 'mshop_stock', 'idx_mssto_sid_backdate' )
			->dropIndex( 'mshop_stock_type', 'unq_msstoty_sid_dom_code' )
			->dropIndex( 'mshop_stock_type', 'idx_msstoty_sid_status_pos' )
			->dropIndex( 'mshop_stock_type', 'idx_msstoty_sid_label' )
			->dropIndex( 'mshop_stock_type', 'idx_msstoty_sid_code' );
	}
}
