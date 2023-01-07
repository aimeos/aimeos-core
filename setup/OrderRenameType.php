<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 */


namespace Aimeos\Upscheme\Task;


class OrderRenameType extends Base
{
	public function before() : array
	{
		return ['Order'];
	}


	public function up()
	{
		$db = $this->db( 'db-order' );

		if( $db->hasTable( 'mshop_order' ) && !$db->hasColumn( 'mshop_order', 'channel' ) )
		{
			$this->info( 'Rename "type" to "channel" in "mshop_order" table', 'vv' );

			$db->dropIndex( 'mshop_order', 'idx_msord_sid_type' )
				->renameColumn( 'mshop_order', 'type', 'channel' );
		}
	}
}
