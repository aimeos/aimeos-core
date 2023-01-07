<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2023
 */


namespace Aimeos\Upscheme\Task;


class IndexRemoveIndexes extends Base
{
	public function before() : array
	{
		return ['Index'];
	}


	public function up()
	{
		$this->info( 'Remove old indexes from mshop_index_* tables', 'vv' );

		$this->db( 'db-product' )
			->dropIndex( 'mshop_index_supplier', 'unq_msindsup_p_sid_supid_lt_po' );
	}
}
