<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
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
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return [];
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

		$start = 0;
		$conn = $this->acquire( $dbdomain );
		$select = 'SELECT "id", "interval", "next", "ctime" FROM "mshop_subscription" WHERE "period" = 0 LIMIT 1000 OFFSET :offset';
		$update = 'UPDATE "mshop_subscription" SET "period" = ? WHERE "id" = ?';

		$stmt = $conn->create( $update, \Aimeos\MW\DB\Connection\Base::TYPE_PREP );

		do
		{
			$map = [];
			$count = 0;
			$today = date( 'Y-m-d' );
			$result = $conn->create( str_replace( ':offset', $start, $select ) )->execute();

			while( ( $row = $result->fetch() ) !== false ) {
				$map[$row['id']] = $row;
			}

			foreach( $map as $id => $entry )
			{
				$period = 0;
				$interval = new \DateInterval( $entry['interval'] );

				do
				{
					$period++;
					$entry['ctime'] = date_create( $entry['ctime'] )->add( $interval )->format( 'Y-m-d' );
				}
				while( $entry['ctime'] < $today );

				$stmt->bind( 1, $period );
				$stmt->bind( 2, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->execute()->finish();
			}

			$count = count( $map );
			$start += $count;
		}
		while( $count === 1000 );

		$this->release( $conn, $dbdomain );

		$this->status( 'done' );
	}
}
