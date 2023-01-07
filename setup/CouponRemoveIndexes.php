<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class CouponRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Coupon'];
	}


	public function up()
	{
		$this->info( 'Remove coupon indexes with siteid column first', 'vv' );

		$this->db( 'db-coupon' )
			->dropIndex( 'mshop_coupon', 'idx_mscou_sid_stat_start_end' )
			->dropIndex( 'mshop_coupon', 'idx_mscou_sid_provider' )
			->dropIndex( 'mshop_coupon', 'idx_mscou_sid_label' )
			->dropIndex( 'mshop_coupon', 'idx_mscou_sid_start' )
			->dropIndex( 'mshop_coupon', 'idx_mscou_sid_end' )
			->dropIndex( 'mshop_coupon_code', 'unq_mscouco_sid_code' )
			->dropIndex( 'mshop_coupon_code', 'idx_mscouco_sid_ct_start_end' )
			->dropIndex( 'mshop_coupon_code', 'idx_mscouco_sid_start' )
			->dropIndex( 'mshop_coupon_code', 'idx_mscouco_sid_end' );
	}
}
