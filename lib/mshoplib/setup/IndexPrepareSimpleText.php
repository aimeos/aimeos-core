<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\Upscheme\Task;


class IndexPrepareSimpleText extends Base
{
	public function before() : array
	{
		return ['Index'];
	}


	public function up()
	{
		$db = $this->db( 'db-product' );

		if( !$db->hasIndex( 'mshop_index_text', 'unq_msindte_p_s_tid_lt' ) ) {
			return;
		}

		$this->info( 'Prepare mshop_index_text table for simplification', 'v' );

		$db->dropTable( 'mshop_index_text' );
	}
}
