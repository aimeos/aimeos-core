<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\Upscheme\Task;


class CouponMigrateBasetValues extends Base
{
	public function after() : array
	{
		return ['Coupon'];
	}


	public function up()
	{
		$db = $this->db( 'db-coupon' );

		if( !$db->hasTable( 'mshop_coupon' ) ) {
			return;
		}

		$this->info( 'Migrating basketvalues configuration in coupon table', 'v' );

		$db->stmt()->update( 'mshop_coupon' )
			->set( 'provider', 'REPLACE("provider", \'BasketValues\', \'Basket\')' )
			->set( 'config', 'REPLACE("config", \'basketvalues\', \'basket\')' )
			->where( 'provider LIKE ?' )->orWhere( 'config LIKE ?' )
			->setParameters( ['%BasketValues%', '%basketvalues%'] );
	}
}
