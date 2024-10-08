<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2023
 */


namespace Aimeos\Upscheme\Task;


class OrderAddBaseProductCurrencyid extends Base
{
	public function after() : array
	{
		return ['OrderConnectTables'];
	}


	public function before() : array
	{
		return ['Order'];
	}


	public function up()
	{
		$db = $this->db( 'db-order' );

		if( !$db->hasTable( ['mshop_order_base', 'mshop_order_base_product'] )
			|| !$db->hasColumn( 'mshop_order_base_product', 'baseid' )
		) {
			return;
		}

		$this->info( 'Adding currency ID to order base product table', 'vv' );

		$db->exec( '
			UPDATE mshop_order_base_product SET currencyid = (
				SELECT ob.currencyid FROM mshop_order_base ob WHERE ob.id = baseid
			) WHERE currencyid = \'\'  OR currencyid = \'   \'
		' );
	}
}
