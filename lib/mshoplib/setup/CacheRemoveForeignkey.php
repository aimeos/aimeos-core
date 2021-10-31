<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
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
		$this->info( 'Remove foreign key "fk_macac_tid_tsid" from "madmin_cache_tag"', 'v' );

		$this->db( 'db-cache' )->dropForeign( 'madmin_cache_tag', 'fk_macac_tid_tsid' );
	}
}
