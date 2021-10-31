<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Upscheme\Task;


class OrderMigrateProductSupplier extends Base
{
	public function before() : array
	{
		return ['Order'];
	}


	public function up()
	{
		$db = $this->db( 'db-order' );

		if( !$db->hasColumn( 'mshop_order_base_product', 'suppliercode' ) ) {
			return;
		}

		$this->info( 'Migrate supplier code to ID and name in order product table', 'v' );

		$this->db()->table( 'mshop_order_base_product', function( $table ) {
			$table->refid( 'supplierid' )->default( '' );
			$table->string( 'suppliername' )->default( '' );
		} )->up();

		$dbm = $this->context()->db();
		$conn = $dbm->acquire( 'db-order' );
		$sconn = $dbm->acquire( 'db-supplier' );

		$stmt = $conn->create(
			'UPDATE "mshop_order_base_product" SET "supplierid" = ?, "suppliername" = ? WHERE "suppliercode" = ?'
		);
		$result = $sconn->create( 'SELECT "id", "code", "label" FROM "mshop_supplier"' )->execute();

		while( $row = $result->fetch() )
		{
			$stmt->bind( 1, $row['id'] );
			$stmt->bind( 2, $row['label'] );
			$stmt->bind( 3, $row['code'] );

			$stmt->execute()->finish();
		}

		$dbm->release( $sconn, 'db-supplier' );
		$dbm->release( $conn, 'db-order' );
	}
}
