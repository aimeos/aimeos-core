<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
 */


namespace Aimeos\Upscheme\Task;


class OrderMigrateStatus extends Base
{
	public function before() : array
	{
		return ['Order'];
	}


	public function up()
	{
		$this->info( 'Migrate order product payment/delivery status', 'v' );

		$db = $this->db( 'db-order' );

		if( $db->hasColumn( 'mshop_order', 'statuspayment' ) ) {
			$db->update( 'mshop_order', ['statuspayment' => -1], ['statuspayment' => null] );
		}

		if( $db->hasColumn( 'mshop_order', 'statusdelivery' ) ) {
			$db->update( 'mshop_order', ['statusdelivery' => -1], ['statusdelivery' => null] );
		}

		if( $db->hasColumn( 'mshop_order_base_product', 'statuspayment' ) ) {
			$db->update( 'mshop_order_base_product', ['statuspayment' => -1], ['statuspayment' => null] );
		}

		if( $db->hasColumn( 'mshop_order_base_product', 'statusdelivery' ) ) {
			$db->update( 'mshop_order_base_product', ['statusdelivery' => -1], ['statusdelivery' => null] );
		}
	}
}
