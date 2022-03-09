<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2022
 */


namespace Aimeos\Upscheme\Task;


class CouponMigrateBasetValues extends Base
{
	public function before() : array
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
			->set( 'provider', 'REPLACE(' . $db->qi( 'provider' ) . ', \'BasketValues\', \'Basket\')' )
			->set( 'config', 'REPLACE(' . $db->qi( 'config' ) . ', \'basketvalues\', \'basket\')' )
			->where( $db->qi( 'provider' ) . ' LIKE \'%BasketValues%\'' )
			->orWhere( $db->qi( 'config' ) . ' LIKE \'%basketvalues%\'' )
			->execute();
	}
}
