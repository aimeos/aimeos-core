<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Updates period count in subscriptions
 */
class SubscriptionMigratePeriod extends \Aimeos\MW\Setup\Task\Base
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
		$dbdomain = 'db-order';
		$this->msg( 'Updating period count in subscriptions', 0 );

		if( $this->getSchema( $dbdomain )->tableExists( 'mshop_subscription' ) === false )
		{
			$this->status( 'OK' );
			return;
		}

		$select = 'SELECT "id", "interval", "end", "ctime" FROM "mshop_subscription" WHERE "period" = 0';
		$update = 'UPDATE "mshop_subscription" SET "period" = ? WHERE "id" = ?';

		$cselect = $this->acquire( $dbdomain );
		$cupdate = $this->acquire( $dbdomain );

		$stmt = $cupdate->create( $update );
		$result = $cselect->create( $select )->execute();

		while( ( $row = $result->fetch() ) !== null )
		{
			$period = 0;
			$end = $row['end'] ?: date( 'Y-m-d' );
			$interval = new \DateInterval( $row['interval'] );

			do
			{
				$period++;
				$row['ctime'] = date_create( $row['ctime'] )->add( $interval )->format( 'Y-m-d' );
			}
			while( $row['ctime'] < $end );

			$stmt->bind( 1, $period );
			$stmt->bind( 2, $row['id'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->execute()->finish();
		}

		$this->release( $cupdate, $dbdomain );
		$this->release( $cselect, $dbdomain );

		$this->status( 'done' );
	}
}
