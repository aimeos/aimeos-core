<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrates the BasketValues decorator in coupon table
 */
class CouponMigrateBasetValues extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Migrate database schema
	 */
	public function migrate()
	{
		$dbdomain = 'db-coupon';
		$this->msg( 'Migrating basketvalues configuration in coupon table', 0 );

		if( $this->getSchema( $dbdomain )->tableExists( 'mshop_coupon' ) === false )
		{
			$this->status( 'OK' );
			return;
		}

		$update = '
			UPDATE "mshop_coupon"
			SET "provider" = REPLACE("provider", \'BasketValues\', \'Basket\'),
				"config" = REPLACE("config", \'basketvalues\', \'basket\')
			WHERE "provider" LIKE \'%BasketValues%\' OR "config" LIKE \'%basketvalues%\'
		';

		$conn = $this->acquire( $dbdomain );
		$conn->create( $update )->execute();
		$this->release( $conn, $dbdomain );

		$this->status( 'done' );
	}
}
