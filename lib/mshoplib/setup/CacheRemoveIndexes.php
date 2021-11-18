<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
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
		$this->info( 'Remove wrong "idx_majob_expire" and "fk_macac_tid" cache indexes', 'v' );

		$this->db( 'db-cache' )
			->dropIndex( 'madmin_cache', 'idx_majob_expire' )
			->dropIndex( 'madmin_cache_tag', 'fk_macac_tid' );
	}
}
