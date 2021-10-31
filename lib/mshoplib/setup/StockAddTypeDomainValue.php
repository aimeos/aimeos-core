<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 */


namespace Aimeos\Upscheme\Task;


class StockAddTypeDomainValue extends Base
{
	public function after() : array
	{
		return ['Stock'];
	}


	public function up()
	{
		$db = $this->db( 'db-product' );

		if( !$db->hasTable( 'mshop_stock_type' ) ) {
			return;
		}

		$this->info( 'Add stock type domain values', 'v' );

		$db->exec( 'UPDATE mshop_stock_type SET domain=\'product\' WHERE domain=\'\'' );
	}
}
