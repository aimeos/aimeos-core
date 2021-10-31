<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\Upscheme\Task;


class IndexPrepareSimplePrice extends Base
{
	public function before() : array
	{
		return ['Index'];
	}


	public function up()
	{
		$db = $this->db( 'db-product' );

		if( !$db->hasIndex( 'mshop_index_price', 'unq_msindpr_p_s_prid_lt' ) ) {
			return;
		}

		$this->info( 'Prepare mshop_index_price table for simplification', 'v' );

		$db->dropTable( 'mshop_index_price' );
	}
}
