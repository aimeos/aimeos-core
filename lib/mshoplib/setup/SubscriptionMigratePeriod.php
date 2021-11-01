<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\Upscheme\Task;


class SubscriptionMigratePeriod extends Base
{
	public function after() : array
	{
		return ['Subscription'];
	}


	public function up()
	{
		$this->info( 'Updating period count in subscriptions', 'v' );

		$dbdomain = 'db-order';
		$dbm = $this->context()->db();

		$select = 'SELECT "id", "interval", "end", "ctime" FROM "mshop_subscription" WHERE "period" = 0';
		$update = 'UPDATE "mshop_subscription" SET "period" = ? WHERE "id" = ?';

		$cselect = $dbm->acquire( $dbdomain );
		$cupdate = $dbm->acquire( $dbdomain );

		$stmt = $cupdate->create( $update );
		$result = $cselect->create( $select )->execute();

		while( $row = $result->fetch() )
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

		$dbm->release( $cupdate, $dbdomain );
		$dbm->release( $cselect, $dbdomain );
	}
}
