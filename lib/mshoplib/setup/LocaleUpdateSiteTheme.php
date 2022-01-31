<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2022
 */


namespace Aimeos\Upscheme\Task;


class LocaleUpdateSiteTheme extends Base
{
	public function before() : array
	{
		return ['Locale'];
	}


	public function up()
	{
		$db = $this->db( 'db-locale' );

		if( !$db->hasColumn( 'mshop_locale_site', 'theme' ) ) {
			return;
		}

		$this->info( 'Allow NULL for "theme" column in "mshop_locale_site"', 'v' );

		$db->update( 'mshop_locale_site', ['theme' => null], ['theme' => ''] );
	}
}
