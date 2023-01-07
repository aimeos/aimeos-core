<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 */


namespace Aimeos\Upscheme\Task;


class CacheRemoveIndexes extends Base
{
	public function before() : array
	{
		return ['Cache'];
	}


	public function up()
	{
		$db = $this->db( 'db-cache' );

		if( !$db->hasTable( 'madmin_cache' ) ) {
			return;
		}

		$this->info( 'Remove wrong "idx_majob_expire" and "fk_macac_tid" cache indexes', 'vv' );

		$db->dropIndex( 'madmin_cache', 'idx_majob_expire' )->dropIndex( 'madmin_cache_tag', 'fk_macac_tid' );
	}
}
