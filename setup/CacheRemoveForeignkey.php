<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2023
 */


namespace Aimeos\Upscheme\Task;


class CacheRemoveForeignkey extends Base
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

		$this->info( 'Remove foreign key "fk_macac_tid_tsid" from "madmin_cache_tag"', 'vv' );

		$db->dropForeign( 'madmin_cache_tag', 'fk_macac_tid_tsid' );
	}
}
