<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\Upscheme\Task;


class LocaleRemoveCharConstraints extends Base
{
	public function before() : array
	{
		return ['Locale'];
	}


	public function up()
	{
		$db = $this->db( 'db-locale' );

		if( !$db->hasTable( 'mshop_locale' ) ) {
			return;
		}

		$this->info( 'Remove mshop_locale char constraints', 'v' );

		if( $db->hasForeign( 'mshop_locale', 'fk_msloc_langid' )
			&& $db->hasColumn( 'mshop_locale', 'langid' )
			&& $db->table( 'mshop_locale' )->col( 'langid', 'string' )->fixed()
		) {
			$this->info( 'Checking constraint for "langid"', 'v', 1 );

			$db->dropForeign( 'mshop_locale', 'fk_msloc_langid' );
			$db->dropIndex( 'mshop_locale', 'fk_msloc_langid' );
		}
	}
}
