<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\Upscheme\Task;


class IndexRemoveCtimeEditor extends Base
{
	public function before() : array
	{
		return ['Index'];
	}


	public function up()
	{
		$this->info( 'Remove ctime/editor from index tables', 'v' );

		$tables = ['mshop_index_attribute', 'mshop_index_catalog', 'mshop_index_price', 'mshop_index_supplier', 'mshop_index_text'];
		$db = $this->db( 'db-product' );

		foreach( $tables as $table )
		{
			$this->info( sprintf( 'Checking table "%1$s": ', $table ), 'vv', 1 );
			$db->dropColumn( $table, 'ctime' )->dropColumn( $table, 'editor' );
		}
	}
}
