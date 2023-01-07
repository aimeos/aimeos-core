<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 */


namespace Aimeos\Upscheme\Task;


class LocaleMigrateSite extends Base
{
	public function before() : array
	{
		return ['Locale'];
	}


	public function after() : array
	{
		return ['TablesMigrateSiteid'];
	}


	public function up()
	{
		$db = $this->db( 'db-locale' );

		if( !$db->hasTable( 'mshop_locale' ) || $db->hasColumn( 'mshop_locale', 'site_id' ) ) {
			return;
		}

		$this->info( 'Use "id" column in "mshop_locale_site"', 'vv' );


		$db->dropForeign( 'mshop_locale', 'fk_msloc_siteid' )
			->dropIndex( 'mshop_locale', 'unq_msloc_sid_lang_curr' )
			->dropIndex( 'mshop_locale', 'idx_msloc_sid_curid' )
			->dropIndex( 'mshop_locale', 'idx_msloc_sid_status' )
			->dropIndex( 'mshop_locale', 'idx_msloc_sid_pos' )
			->dropIndex( 'mshop_locale', 'fk_msloc_siteid' );

		$db->table( 'mshop_locale' )->int( 'site_id' )->null( true )->up();


		$result = $db->stmt()->select( 'id', 'siteid' )->from( 'mshop_locale_site' )->execute();
		$db2 = $this->db( 'db-locale', true );

		while( $row = $result->fetch() ) {
			$db2->update( 'mshop_locale', ['site_id' => $row['id']], ['siteid' => $row['siteid']] );
		}

		$db2->close();
	}
}
