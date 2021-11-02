<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\Upscheme\Task;


class CustomerDropAddressForeign extends Base
{
	public function before() : array
	{
		return ['Coupon'];
	}


	public function up()
	{
		$db = $this->db( 'db-customer' );

		if( !$db->hasTable( 'mshop_customer_address' ) ) {
			return;
		}

		$this->info( 'Drop "fk_mscusad_parentid" in customer address table', 'v' );

		$db->dropForeign( 'mshop_customer_address', 'fk_mscusad_parentid' );
		$db->dropIndex( 'mshop_customer_address', 'fk_mscusad_parentid' );
	}
}
