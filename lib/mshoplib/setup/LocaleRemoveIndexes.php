<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Upscheme\Task;


class LocaleRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Locale'];
	}


	public function up()
	{
		$db = $this->db( 'db-locale' );

		if( !$db->hasTable( 'mshop_locale' ) ) {
			return;
		}

		$this->info( 'Remove indexes from mshop_locale', 'v' );

		$db->dropIndex( 'mshop_locale', 'fk_mslocsi_id' );
		$db->dropIndex( 'mshop_locale', 'fk_mslocla_id' );
		$db->dropIndex( 'mshop_locale', 'fk_msloccu_id' );
		$db->dropIndex( 'mshop_locale', 'IDX_628DFA7F2271845' );
		$db->dropIndex( 'mshop_locale', 'IDX_628DFA7F4842F28' );
	}
}
