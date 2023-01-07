<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 */


namespace Aimeos\Upscheme\Task;


class PriceMigrateTaxRateName extends Base
{
	public function after() : array
	{
		return ['PriceMigrateTaxrate'];
	}


	public function up()
	{
		$this->info( 'Migrating taxrate name in price table', 'vv' );

		$db = $this->db( 'db-price' );
		$db->stmt()->update( 'mshop_price' )
			->set( 'taxrate', 'REPLACE(' . $db->qi( 'taxrate' ) . ', \'{"":\', \'{"tax":\')' )
			->where( $db->qi( 'taxrate' ) . ' LIKE \'{"":%\'' )
			->execute();
	}
}
