<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2025
 */


namespace Aimeos\Upscheme\Task;


class LocaleSetSiteParentRoot extends Base
{
	public function before() : array
	{
		return ['Locale'];
	}


	public function up()
	{
		$this->info( 'Set locale site root parent IDs to NULL', 'vv' );

		$this->db( 'db-locale' )->update( 'mshop_locale_site', ['parentid' => null], ['parentid' => 0] );
	}
}
