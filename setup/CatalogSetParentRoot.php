<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2025
 */


namespace Aimeos\Upscheme\Task;


class CatalogSetParentRoot extends Base
{
	public function before() : array
	{
		return ['Catalog'];
	}


	public function up()
	{
		$this->info( 'Set catalog root parent IDs to "0"', 'vv' );

		$db = $this->db( 'db-catalog' );

		if( $db->hasTable( 'mshop_catalog' ) ) {
			$db->update( 'mshop_catalog', ['parentid' => 0], ['parentid' => null] );
		}
	}
}
