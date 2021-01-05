<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds the weekday values in order tables
 */
class OrderAddWeekday extends \Aimeos\MW\Setup\Task\Base
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
		$this->msg( 'Populate weekday column in order table', 0 );

		if( $this->getSchema( $dbdomain )->tableExists( 'mshop_order' ) === false )
		{
			$this->status( 'OK' );
			return;
		}

		$conn = $this->acquire( $dbdomain );
		$select = 'SELECT "id", "ctime" FROM "mshop_order" WHERE "cwday" = \'\'';
		$update = 'UPDATE "mshop_order" SET "cwday" = ? WHERE "id" = ?';

		$stmt = $conn->create( $update );
		$result = $conn->create( $select )->execute();

		while( ( $row = $result->fetch() ) !== null )
		{
			list( $date, $time ) = explode( ' ', $row['ctime'] );

			$stmt->bind( 1, date_create_from_format( 'Y-m-d', $date )->format( 'w' ) );
			$stmt->bind( 2, $row['id'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );

			$stmt->execute()->finish();
		}

		$this->release( $conn, $dbdomain );

		$this->status( 'done' );
	}
}
