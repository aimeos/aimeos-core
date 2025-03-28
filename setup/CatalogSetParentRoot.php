<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2025
 */


namespace Aimeos\Upscheme\Task;


class CatalogSetParentRoot extends Base
{
	public function after() : array
	{
		return ['Catalog'];
	}


	public function up()
	{
		$this->info( 'Set catalog root parent IDs to NULL', 'vv' );

		$this->db( 'db-catalog' )->update( 'mshop_catalog', ['parentid' => null], ['parentid' => 0] );
	}
}
