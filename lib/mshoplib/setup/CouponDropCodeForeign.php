<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\Upscheme\Task;


class CouponDropCodeForeign extends Base
{
	public function before() : array
	{
		return ['Coupon'];
	}


	public function up()
	{
		$db = $this->db( 'db-coupon' );

		if( !$db->hasTable( 'mshop_coupon' ) ) {
			return;
		}

		$this->info( 'Drop "fk_mscouco_parentid" in coupon code table', 'v' );

		$db->dropForeign( 'mshop_coupon_code', 'fk_mscouco_parentid' );
		$db->dropIndex( 'mshop_coupon_code', 'fk_mscouco_parentid' );
	}
}
