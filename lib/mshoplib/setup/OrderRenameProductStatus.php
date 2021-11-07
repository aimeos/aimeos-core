<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Upscheme\Task;


class OrderRenameProductStatus extends Base
{
	public function before() : array
	{
		return ['Order'];
	}


	public function up()
	{
		$db = $this->db( 'db-order' );

		if( $db->hasTable( 'mshop_order_base_product' )
			&& !$db->hasColumn( 'mshop_order_base_product', 'statusdelivery' )
		) {
			$this->info( 'Rename "status" to "statusdelivery" in "mshop_order_base_product" table', 'v' );

			$db->renameColumn( 'mshop_order_base_product', 'status', 'statusdelivery' );
		}
	}
}
