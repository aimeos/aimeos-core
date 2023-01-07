<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class LocaleUpdateSiteTheme extends Base
{
	public function after() : array
	{
		return ['Locale'];
	}


	public function up()
	{
		$this->info( 'Allow NULL for "theme" column in "mshop_locale_site"', 'vv' );

		$this->db( 'db-locale' )->update( 'mshop_locale_site', ['theme' => null], ['theme' => ''] );
	}
}
