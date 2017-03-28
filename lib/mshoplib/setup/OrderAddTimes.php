<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds the time values in order tables
 */
class OrderAddTimes extends \Aimeos\MW\Setup\Task\Base
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
		$this->msg( 'Adding time columns to order table', 0 );

		if( $this->getSchema( $dbdomain )->tableExists( 'mshop_order' ) === false )
		{
			$this->status( 'OK' );
			return;
		}

		$start = 0;
		$conn = $this->getConnection( $dbdomain );
		$select = 'SELECT "id", "ctime" FROM "mshop_order" WHERE "cdate" = \'\' LIMIT 1000 OFFSET :offset';
		$update = 'UPDATE "mshop_order" SET "cdate" = ?, "cmonth" = ?, "cweek" = ?, "chour" = ? WHERE "id" = ?';

		$stmt = $conn->create( $update, \Aimeos\MW\DB\Connection\Base::TYPE_PREP );

		do
		{
			$count = 0;
			$map = [];
			$sql = str_replace( ':offset', $start, $select );
			$result = $conn->create( $sql )->execute();

			while( ( $row = $result->fetch() ) !== false )
			{
				$map[$row['id']] = $row['ctime'];
				$count++;
			}

			foreach( $map as $id => $ctime )
			{
				list( $date, $time ) = explode( ' ', $ctime );

				$stmt->bind( 1, $date );
				$stmt->bind( 2, substr( $date, 0, 7 ) );
				$stmt->bind( 3, date_create_from_format( 'Y-m-d', $date )->format( 'Y-W' )  );
				$stmt->bind( 4, substr( $time, 0, 2 ) );
				$stmt->bind( 5, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );

				$stmt->execute()->finish();
			}

			$start += $count;
		}
		while( $count === 1000 );

		$this->status( 'done' );
	}
}
