<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2023
 */


namespace Aimeos\Upscheme\Task;


class OrderMigrateTaxrate extends Base
{
	public function after() : array
	{
		return ['Order'];
	}


	public function up()
	{
		$this->info( 'Migrating taxrate columns in order tables', 'vv' );

		$conn = $this->context()->db( 'db-order' );

		$this->info( 'Migrating taxrate column in order base product table', 'vv', 1 );

		$select = 'SELECT "id", "taxrate" FROM "mshop_order_product" WHERE "taxrate" NOT LIKE \'{%\'';
		$update = 'UPDATE "mshop_order_product" SET "taxrate" = ? WHERE "id" = ?';

		$stmt = $conn->create( $update );
		$result = $conn->create( $select )->execute();

		while( ( $row = $result->fetch() ) !== null )
		{
			$stmt->bind( 1, json_encode( ['' => $row['taxrate']], JSON_FORCE_OBJECT ) );
			$stmt->bind( 2, $row['id'], \Aimeos\Base\DB\Statement\Base::PARAM_INT );

			$stmt->execute()->finish();
		}


		$this->info( 'Migrating taxrate column in order base service table', 'vv', 1 );

		$select = 'SELECT "id", "taxrate" FROM "mshop_order_service" WHERE "taxrate" NOT LIKE \'{%\'';
		$update = 'UPDATE "mshop_order_service" SET "taxrate" = ? WHERE "id" = ?';

		$stmt = $conn->create( $update );

		$result = $conn->create( $select )->execute();

		while( ( $row = $result->fetch() ) !== null )
		{
			$stmt->bind( 1, json_encode( ['' => $row['taxrate']], JSON_FORCE_OBJECT ) );
			$stmt->bind( 2, $row['id'], \Aimeos\Base\DB\Statement\Base::PARAM_INT );

			$stmt->execute()->finish();
		}
	}
}
