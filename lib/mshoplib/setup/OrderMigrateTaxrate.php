<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
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
		$db = $this->db();
		$dbm = $this->context()->db();
		$dbdomain = 'db-order';

		$this->info( 'Migrating taxrate columns in order tables', 'v' );
		$this->info( 'Migrating taxrate column in order base product table', 'vv', 1 );

		if( $db->hasTable( 'mshop_order_base_product' ) )
		{
			$conn = $dbm->acquire( $dbdomain );
			$select = 'SELECT "id", "taxrate" FROM "mshop_order_base_product" WHERE "taxrate" NOT LIKE \'{%\'';
			$update = 'UPDATE "mshop_order_base_product" SET "taxrate" = ? WHERE "id" = ?';

			$stmt = $conn->create( $update );
			$result = $conn->create( $select )->execute();

			while( ( $row = $result->fetch() ) !== null )
			{
				$stmt->bind( 1, json_encode( ['' => $row['taxrate']], JSON_FORCE_OBJECT ) );
				$stmt->bind( 2, $row['id'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );

				$stmt->execute()->finish();
			}

			$dbm->release( $conn, $dbdomain );
		}


		$this->info( 'Migrating taxrate column in order base service table', 'vv', 1 );

		if( $db->hasTable( 'mshop_order_base_service' ) )
		{
			$conn = $dbm->acquire( $dbdomain );
			$select = 'SELECT "id", "taxrate" FROM "mshop_order_base_service" WHERE "taxrate" NOT LIKE \'{%\'';
			$update = 'UPDATE "mshop_order_base_service" SET "taxrate" = ? WHERE "id" = ?';

			$stmt = $conn->create( $update );

				$result = $conn->create( $select )->execute();

				while( ( $row = $result->fetch() ) !== null )
				{
					$stmt->bind( 1, json_encode( ['' => $row['taxrate']], JSON_FORCE_OBJECT ) );
					$stmt->bind( 2, $row['id'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );

					$stmt->execute()->finish();
				}

			$dbm->release( $conn, $dbdomain );
		}
	}
}
