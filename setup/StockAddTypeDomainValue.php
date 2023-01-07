<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2023
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
		$this->info( 'Add stock type domain values', 'vv' );

		$this->db( 'db-stock' )->exec( 'UPDATE mshop_stock_type SET domain=\'product\' WHERE domain=\'\'' );
	}
}
