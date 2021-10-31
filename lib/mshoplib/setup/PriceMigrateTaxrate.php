<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\Upscheme\Task;


class PriceMigrateTaxrate extends Base
{
	public function after() : array
	{
		return ['Price'];
	}


	public function up()
	{
		$db = $this->db( 'db-price' );

		if( !$db->hasTable( 'mshop_price' ) ) {
			return;
		}

		$this->info( 'Migrating taxrate column in price table', 'v' );

		$dbm = $this->context()->db();
		$conn = $dbm->acquire( 'db-price' );

		$select = 'SELECT "id", "taxrate" FROM "mshop_price" WHERE "taxrate" NOT LIKE \'{%\'';
		$update = 'UPDATE "mshop_price" SET "taxrate" = ? WHERE "id" = ?';

		$stmt = $conn->create( $update );
		$result = $conn->create( $select )->execute();

		while( ( $row = $result->fetch() ) !== null )
		{
			$stmt->bind( 1, json_encode( ['' => $row['taxrate']], JSON_FORCE_OBJECT ) );
			$stmt->bind( 2, $row['id'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );

			$stmt->execute()->finish();
		}

		$dbm->release( $conn, 'db-price' );
	}
}
