<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\Upscheme\Task;


class IndexDropTextWithoutId extends Base
{
	public function after() : array
	{
		return ['Index'];
	}


	public function up()
	{
		$db = $this->db( 'db-product' );

		if( $db->hasColumn( 'mshop_index_text', 'id' ) ) {
			return;
		}

		$this->info( 'Droping mshop_index_text table without ID column', 'v' );

		$db->dropTable( 'mshop_index_text' );
	}
}
