<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024
 */


namespace Aimeos\Upscheme\Task;


class OrderMoveBasket extends Base
{
	public function before() : array
	{
		return ['Basket', 'Order'];
	}


	public function up()
	{
		$db = $this->db( 'db-order' );

		if( !$db->hasTable( 'mshop_order_basket' ) ) {
			return;
		}

		$this->info( 'Move baskets to own domain', 'vv' );

		$db->dropIndex( 'mshop_order_basket', 'idx_msordca_custid' )
			->dropIndex( 'mshop_order_basket', 'idx_msordca_mtime' )
			->dropIndex( 'mshop_order_basket', 'pk_msordca_id' );

		$db->renameTable( 'mshop_order_basket', 'mshop_basket' );
	}
}
