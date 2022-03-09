<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2022
 */


namespace Aimeos\Upscheme\Task;


class PriceMigrateTaxRateName extends Base
{
	public function before() : array
	{
		return ['Price'];
	}


	public function after() : array
	{
		return ['PriceMigrateTaxrate'];
	}


	public function up()
	{
		$db = $this->db( 'db-price' );

		if( !$db->hasTable( 'mshop_price' ) ) {
			return;
		}

		$this->info( 'Migrating taxrate name in price table', 'v' );

		$db->stmt()->update( 'mshop_price' )
			->set( 'taxrate', 'REPLACE(' . $db->qi( 'taxrate' ) . ', \'{"":\', \'{"tax":\')' )
			->where( $db->qi( 'taxrate' ) . ' LIKE \'{"":%\'' )
			->execute();
	}
}
