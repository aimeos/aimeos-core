<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2023
 */


namespace Aimeos\Upscheme\Task;


class OrderAddWeekday extends Base
{
	public function after() : array
	{
		return ['Order'];
	}


	public function up()
	{
		$db = $this->db( 'db-order' );

		if( !$db->hasTable( 'mshop_order' ) ) {
			return;
		}

		$this->info( 'Populate weekday column in order table', 'vv' );

		$conn = $this->context()->db( 'db-order' );

		$select = 'SELECT "id", "ctime" FROM "mshop_order" WHERE "cwday" = \'\'';
		$update = 'UPDATE "mshop_order" SET "cwday" = ? WHERE "id" = ?';

		$stmt = $conn->create( $update );
		$result = $conn->create( $select )->execute();

		while( ( $row = $result->fetch() ) !== null )
		{
			list( $date, $time ) = explode( ' ', $row['ctime'] );

			$stmt->bind( 1, date_create_from_format( 'Y-m-d', $date )->format( 'w' ) );
			$stmt->bind( 2, $row['id'], \Aimeos\Base\DB\Statement\Base::PARAM_INT );

			$stmt->execute()->finish();
		}
	}
}
