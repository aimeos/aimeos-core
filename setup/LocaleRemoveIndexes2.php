<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class LocaleRemoveIndexes2 extends Base
{
	public function after() : array
	{
		return ['Locale'];
	}


	public function up()
	{
		$this->info( 'Remove locale indexes with siteid column first', 'vv' );

		$this->db( 'db-locale' )
			->dropIndex( 'mshop_locale', 'unq_msloc_sid_lang_curr' )
			->dropIndex( 'mshop_locale', 'idx_msloc_sid_status' )
			->dropIndex( 'mshop_locale', 'idx_msloc_sid_curid' )
			->dropIndex( 'mshop_locale', 'idx_msloc_sid_pos' );
	}
}
