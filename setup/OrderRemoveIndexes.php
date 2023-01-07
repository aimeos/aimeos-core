<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class OrderRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Order'];
	}


	public function up()
	{
		$this->info( 'Remove order indexes with siteid column first', 'vv' );

		$this->db( 'db-order' )
			->dropIndex( 'mshop_order', 'idx_msord_sid_channel' )
			->dropIndex( 'mshop_order', 'idx_msord_sid_ctime_pstat' )
			->dropIndex( 'mshop_order', 'idx_msord_sid_mtime_pstat' )
			->dropIndex( 'mshop_order', 'idx_msord_sid_mtime_dstat' )
			->dropIndex( 'mshop_order', 'idx_msord_sid_dstatus' )
			->dropIndex( 'mshop_order', 'idx_msord_sid_ddate' )
			->dropIndex( 'mshop_order', 'idx_msord_sid_pdate' )
			->dropIndex( 'mshop_order', 'idx_msord_sid_editor' )
			->dropIndex( 'mshop_order', 'idx_msord_sid_cdate' )
			->dropIndex( 'mshop_order', 'idx_msord_sid_cmonth' )
			->dropIndex( 'mshop_order', 'idx_msord_sid_cweek' )
			->dropIndex( 'mshop_order', 'idx_msord_sid_cwday' )
			->dropIndex( 'mshop_order', 'idx_msord_sid_chour' )
			->dropIndex( 'mshop_order_base', 'idx_msordba_sid_ctime' )
			->dropIndex( 'mshop_order_status', 'idx_msordstatus_val_sid' )
			->dropIndex( 'mshop_order_base_address', 'idx_msordbaad_sid_bid_typ' )
			->dropIndex( 'mshop_order_base_address', 'idx_msordbaad_bid_sid_lname' )
			->dropIndex( 'mshop_order_base_address', 'idx_msordbaad_bid_sid_addr1' )
			->dropIndex( 'mshop_order_base_address', 'idx_msordbaad_bid_sid_postal' )
			->dropIndex( 'mshop_order_base_address', 'idx_msordbaad_bid_sid_city' )
			->dropIndex( 'mshop_order_base_address', 'idx_msordbaad_bid_sid_email' )
			->dropIndex( 'mshop_order_base_product', 'idx_msordbapr_bid_sid_pid' )
			->dropIndex( 'mshop_order_base_product', 'idx_msordbapr_bid_sid_pcd' )
			->dropIndex( 'mshop_order_base_product', 'idx_msordbapr_bid_sid_qtyo' )
			->dropIndex( 'mshop_order_base_product', 'idx_msordbapr_ct_sid_pid_bid' )
			->dropIndex( 'mshop_order_base_service', 'unq_msordbase_bid_sid_cd_typ' )
			->dropIndex( 'mshop_order_base_service', 'idx_msordbase_sid_code_type' )
			->dropIndex( 'mshop_order_base_coupon', 'idx_msordbaco_bid_sid_code' );
	}
}
