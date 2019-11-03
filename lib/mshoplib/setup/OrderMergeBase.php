<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Merges the order and order base table
 */
class OrderMergeBase extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return ['TablesCreateMShop'];
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
	 * Executes the table cleanup
	 */
	public function clean()
	{
		$sm = $this->getSchemaManager( 'db-order' );
		$src = $sm->createSchema();
		$dest = clone $src;

		$tables = [
			'mshop_order_base', 'mshop_order_base_address', 'mshop_order_base_coupon',
			'mshop_order_base_product', 'mshop_order_base_product_attr',
			'mshop_order_base_service', 'mshop_order_base_service_attr'
		];

		foreach( $tables as $table )
		{
			if( $src->hasTable( $table ) ) {
				$dest->dropTable( $table->getName() );
			}
		}

		$this->update( $src, $dest, 'db-order' );
	}


	/**
	 * Executes the migration
	 */
	public function migrate()
	{
		$this->msg( 'Migrate order domain', 0 ); $this->status( '' );

		$sm = $this->getSchemaManager( 'db-order' );
		$src = $sm->createSchema();

		if( !$src->hasTable( 'mshop_order' ) || !$src->getTable( 'mshop_order' )->hasColumn( 'baseid' ) ) {
			return $this->status( 'OK' );
		}

		if( $src->hasTable( 'mshop_order_base' ) ) {
			$this->updateBase();
		}

		if( $src->hasTable( 'mshop_order_base_address' ) ) {
			$this->updateAddresses();
		}

		if( $src->hasTable( 'mshop_order_base_coupon' ) ) {
			$this->updateCoupons();
		}

		if( $src->hasTable( 'mshop_order_base_product' ) ) {
			$this->updateProducts();
		}

		if( $src->hasTable( 'mshop_order_base_product_attr' ) ) {
			$this->updateProductAttributes();
		}

		if( $src->hasTable( 'mshop_order_base_service' ) ) {
			$this->updateServices();
		}

		if( $src->hasTable( 'mshop_order_base_service_attr' ) ) {
			$this->updateServiceAttributes();
		}

		$this->status( 'done' );
	}


	protected function updateBase()
	{
		$this->msg( 'Merge mshop_order and mshop_order_base tables', 1 );

		$conn = $this->acquire( 'db-order' );
		$conn2 = $this->acquire( 'db-order' );

		$select = 'SELECT * FROM "mshop_order_base" LIMIT 1000 OFFSET :offset';
		$update = '
			UPDATE "mshop_order"
			SET "sitecode" = ?, "customerid" = ?, "langid" = ?, "currencyid" = ?, "price" = ?, "costs" = ?,
				"rebate" = ?, "tax" = ?, "taxflag" = ?, "customerref" = ?, "comment" = ?
			WHERE "baseid" = ?
		';

		$stmt = $conn2->create( $update, \Aimeos\MW\DB\Connection\Base::TYPE_PREP );
		$start = 0;

		$conn2->begin();

		do
		{
			$count = 0;
			$result = $conn->create( str_replace( ':offset', $start, $select ) )->execute();

			while( ( $row = $result->fetch() ) !== false )
			{
				$stmt->bind( 1, $row['sitecode'] );
				$stmt->bind( 2, $row['customerid'] );
				$stmt->bind( 3, $row['langid'] );
				$stmt->bind( 4, $row['currencyid'] );
				$stmt->bind( 5, $row['price'] );
				$stmt->bind( 6, $row['costs'] );
				$stmt->bind( 7, $row['rebate'] );
				$stmt->bind( 8, $row['tax'] );
				$stmt->bind( 9, $row['taxflag'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 10, $row['customerref'] );
				$stmt->bind( 11, $row['comment'] );
				$stmt->bind( 12, $row['id'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );

				$stmt->execute()->finish();
				$count++;
			}

			$start += $count;
		}
		while( $count === 1000 );

		$conn2->commit();

		$this->release( $conn2, 'db-order' );
		$this->release( $conn, 'db-order' );

		$this->status( 'done' );
	}


	protected function updateAddresses()
	{
		$this->msg( 'Copy mshop_order_base_address to mshop_order_address table', 1 );

		$conn = $this->acquire( 'db-order' );
		$conn2 = $this->acquire( 'db-order' );

		$select = '
			SELECT o."id" AS "ordid", oba.* FROM "mshop_order" AS o
			JOIN "mshop_order_base_address" AS oba ON oba."baseid" = o."baseid"
			LIMIT 1000 OFFSET :offset
		';
		$update = '
			INSERT "mshop_order_address"
			SET "orderid" = ?, "siteid" = ?, "addrid" = ?, "type" = ?, "salutation" = ?,
				"company" = ?, "vatid" = ?, "title" = ?, "firstname" = ?, "lastname" = ?,
				"address1" = ?, "address2" = ?, "address3" = ?, "postal" = ?, "city" = ?,
				"state" = ?, "langid" = ?, "countryid" = ?, "telephone" = ?, "telefax" = ?,
				"email" = ?, "website" = ?, "longitude" = ?, "latitude" = ?, "pos" = ?,
				"mtime" = ?, "ctime" = ?, "editor" = ?, "id" = ?
		';

		if( $conn->create( 'SELECT "id" FROM "mshop_order_address" LIMIT 1' )->execute()->fetch() === false )
		{
			$stmt = $conn2->create( $update, \Aimeos\MW\DB\Connection\Base::TYPE_PREP );
			$start = 0;

			$conn2->begin();

			do
			{
				$count = 0;
				$result = $conn->create( str_replace( ':offset', $start, $select ) )->execute();

				while( ( $row = $result->fetch() ) !== false )
				{
					$stmt->bind( 1, $row['ordid'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 2, $row['siteid'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 3, $row['addrid'] );
					$stmt->bind( 4, $row['type'] );
					$stmt->bind( 5, $row['salutation'] );
					$stmt->bind( 6, $row['company'] );
					$stmt->bind( 7, $row['vatid'] );
					$stmt->bind( 8, $row['title'] );
					$stmt->bind( 9, $row['firstname'] );
					$stmt->bind( 10, $row['lastname'] );
					$stmt->bind( 11, $row['address1'] );
					$stmt->bind( 12, $row['address2'] );
					$stmt->bind( 13, $row['address3'] );
					$stmt->bind( 14, $row['postal'] );
					$stmt->bind( 15, $row['city'] );
					$stmt->bind( 16, $row['state'] );
					$stmt->bind( 17, $row['langid'] );
					$stmt->bind( 18, $row['countryid'] );
					$stmt->bind( 19, $row['telephone'] );
					$stmt->bind( 20, $row['telefax'] );
					$stmt->bind( 21, $row['email'] );
					$stmt->bind( 22, $row['website'] );
					$stmt->bind( 23, $row['longitude'] );
					$stmt->bind( 24, $row['latitude'] );
					$stmt->bind( 25, $row['pos'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 26, $row['mtime'] );
					$stmt->bind( 27, $row['ctime'] );
					$stmt->bind( 28, $row['editor'] );
					$stmt->bind( 29, $row['id'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );

					$stmt->execute()->finish();
					$count++;
				}

				$start += $count;
			}
			while( $count === 1000 );

			$conn2->commit();

			$this->status( 'done' );
		}
		else
		{
			$this->status( 'skip' );
		}

		$this->release( $conn2, 'db-order' );
		$this->release( $conn, 'db-order' );
	}


	protected function updateCoupons()
	{
		$this->msg( 'Copy mshop_order_base_coupon to mshop_order_coupon table', 1 );

		$conn = $this->acquire( 'db-order' );
		$conn2 = $this->acquire( 'db-order' );

		$select = '
			SELECT o."id" AS "ordid", obc.* FROM "mshop_order" AS o
			JOIN "mshop_order_base_coupon" AS obc ON obc."baseid" = o."baseid"
			LIMIT 1000 OFFSET :offset
		';
		$update = '
			INSERT "mshop_order_address"
			SET "orderid" = ?, "siteid" = ?, "ordprodid" = ?, "code" = ?,
				"mtime" = ?, "ctime" = ?, "editor" = ?, "id" = ?
		';

		if( $conn->create( 'SELECT "id" FROM "mshop_order_coupon" LIMIT 1' )->execute()->fetch() === false )
		{
			$stmt = $conn2->create( $update, \Aimeos\MW\DB\Connection\Base::TYPE_PREP );
			$start = 0;

			$conn2->begin();

			do
			{
				$count = 0;
				$result = $conn->create( str_replace( ':offset', $start, $select ) )->execute();

				while( ( $row = $result->fetch() ) !== false )
				{
					$stmt->bind( 1, $row['ordid'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 2, $row['siteid'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 3, $row['ordprodid'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 4, $row['code'] );
					$stmt->bind( 5, $row['mtime'] );
					$stmt->bind( 6, $row['ctime'] );
					$stmt->bind( 7, $row['editor'] );
					$stmt->bind( 8, $row['id'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );

					$stmt->execute()->finish();
					$count++;
				}

				$start += $count;
			}
			while( $count === 1000 );

			$conn2->commit();

			$this->status( 'done' );
		}
		else
		{
			$this->status( 'skip' );
		}

		$this->release( $conn2, 'db-order' );
		$this->release( $conn, 'db-order' );
	}


	protected function updateProducts()
	{
		$this->msg( 'Copy mshop_order_base_product to mshop_order_product table', 1 );

		$conn = $this->acquire( 'db-order' );
		$conn2 = $this->acquire( 'db-order' );

		$select = '
			SELECT o."id" AS "ordid", obp.* FROM "mshop_order" AS o
			JOIN "mshop_order_base_product" AS obp ON obp."baseid" = o."baseid"
			LIMIT 1000 OFFSET :offset
		';
		$update = '
			INSERT "mshop_order_product"
			SET "orderid" = ?, "siteid" = ?, "ordprodid" = ?, "ordaddrid" = ?, "type" = ?, "prodid" = ?,
				"prodcode" = ?, "suppliercode" = ?, "stocktype" = ?, "name" = ?, "description" = ?,
				"mediaurl" = ?, "target" = ?, "timeframe" = ?, "quantity" = ?, "currencyid" = ?,
				"price" = ?, "costs" = ?, "rebate" = ?, "tax" = ?, "taxrate" = ?, "taxflag" = ?,
				"flags" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "ctime" = ?, "editor" = ?, "id" = ?
		';

		if( $conn->create( 'SELECT "id" FROM "mshop_order_product" LIMIT 1' )->execute()->fetch() === false )
		{
			$stmt = $conn2->create( $update, \Aimeos\MW\DB\Connection\Base::TYPE_PREP );
			$start = 0;

			$conn2->begin();

			do
			{
				$count = 0;
				$result = $conn->create( str_replace( ':offset', $start, $select ) )->execute();

				while( ( $row = $result->fetch() ) !== false )
				{
					$stmt->bind( 1, $row['ordid'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 2, $row['siteid'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 3, $row['ordprodid'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 4, $row['ordaddrid'] );
					$stmt->bind( 5, $row['type'] );
					$stmt->bind( 6, $row['prodid'] );
					$stmt->bind( 7, $row['prodcode'] );
					$stmt->bind( 8, $row['suppliercode'] );
					$stmt->bind( 9, $row['stocktype'] );
					$stmt->bind( 10, $row['name'] );
					$stmt->bind( 11, $row['description'] );
					$stmt->bind( 12, $row['mediaurl'] );
					$stmt->bind( 13, $row['target'] );
					$stmt->bind( 14, $row['timeframe'] );
					$stmt->bind( 15, $row['quantity'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 16, $row['currencyid'] );
					$stmt->bind( 17, $row['price'] );
					$stmt->bind( 18, $row['costs'] );
					$stmt->bind( 19, $row['rebate'] );
					$stmt->bind( 20, $row['tax'] );
					$stmt->bind( 21, $row['taxrate'] );
					$stmt->bind( 22, $row['taxflag'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 23, $row['flags'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 24, $row['pos'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 25, $row['status'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 26, $row['mtime'] );
					$stmt->bind( 27, $row['ctime'] );
					$stmt->bind( 28, $row['editor'] );
					$stmt->bind( 29, $row['id'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );

					$stmt->execute()->finish();
					$count++;
				}

				$start += $count;
			}
			while( $count === 1000 );

			$conn2->commit();

			$this->status( 'done' );
		}
		else
		{
			$this->status( 'skip' );
		}

		$this->release( $conn2, 'db-order' );
		$this->release( $conn, 'db-order' );
	}


	protected function updateProductAttributes()
	{
		$this->msg( 'Copy mshop_order_base_product_attr to mshop_order_product_attr table', 1 );

		$conn = $this->acquire( 'db-order' );
		$conn2 = $this->acquire( 'db-order' );

		$select = 'SELECT * FROM "mshop_order_base_product_attr" LIMIT 1000 OFFSET :offset';
		$update = '
			INSERT "mshop_order_product_attr"
			SET "ordprodid" = ?, "siteid" = ?, "attrid" = ?, "type" = ?, "code" = ?, "name" = ?,
				"value" = ?, "mtime" = ?, "ctime" = ?, "editor" = ?, "id" = ?
		';

		if( $conn->create( 'SELECT "id" FROM "mshop_order_product_attr" LIMIT 1' )->execute()->fetch() === false )
		{
			$stmt = $conn2->create( $update, \Aimeos\MW\DB\Connection\Base::TYPE_PREP );
			$start = 0;

			$conn2->begin();

			do
			{
				$count = 0;
				$result = $conn->create( str_replace( ':offset', $start, $select ) )->execute();

				while( ( $row = $result->fetch() ) !== false )
				{
					$stmt->bind( 1, $row['ordprodid'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 2, $row['siteid'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 3, $row['attrid'] );
					$stmt->bind( 4, $row['type'] );
					$stmt->bind( 5, $row['code'] );
					$stmt->bind( 6, $row['name'] );
					$stmt->bind( 7, $row['value'] );
					$stmt->bind( 8, $row['mtime'] );
					$stmt->bind( 9, $row['ctime'] );
					$stmt->bind( 10, $row['editor'] );
					$stmt->bind( 11, $row['id'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );

					$stmt->execute()->finish();
					$count++;
				}

				$start += $count;
			}
			while( $count === 1000 );

			$conn2->commit();

			$this->status( 'done' );
		}
		else
		{
			$this->status( 'skip' );
		}

		$this->release( $conn2, 'db-order' );
		$this->release( $conn, 'db-order' );
	}


	protected function updateServices()
	{
		$this->msg( 'Copy mshop_order_base_service to mshop_order_service table', 1 );

		$conn = $this->acquire( 'db-order' );
		$conn2 = $this->acquire( 'db-order' );

		$select = '
			SELECT o."id" AS "ordid", obs.* FROM "mshop_order" AS o
			JOIN "mshop_order_base_service" AS obs ON obs."baseid" = o."baseid"
			LIMIT 1000 OFFSET :offset
		';
		$update = '
			INSERT "mshop_order_service"
			SET "orderid" = ?, "siteid" = ?, "servid" = ?, "type" = ?, "code" = ?, "name" = ?,
				"mediaurl" = ?, "currencyid" = ?, "price" = ?, "costs" = ?, "rebate" = ?, "tax" = ?,
				"taxrate" = ?, "taxflag" = ?, "pos" = ?, "mtime" = ?, "ctime" = ?, "editor" = ?, "id" = ?
		';

		if( $conn->create( 'SELECT "id" FROM "mshop_order_service" LIMIT 1' )->execute()->fetch() === false )
		{
			$stmt = $conn2->create( $update, \Aimeos\MW\DB\Connection\Base::TYPE_PREP );
			$start = 0;

			$conn2->begin();

			do
			{
				$count = 0;
				$result = $conn->create( str_replace( ':offset', $start, $select ) )->execute();

				while( ( $row = $result->fetch() ) !== false )
				{
					$stmt->bind( 1, $row['ordid'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 2, $row['siteid'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 3, $row['servid'] );
					$stmt->bind( 4, $row['type'] );
					$stmt->bind( 5, $row['code'] );
					$stmt->bind( 6, $row['name'] );
					$stmt->bind( 7, $row['mediaurl'] );
					$stmt->bind( 8, $row['currencyid'] );
					$stmt->bind( 9, $row['price'] );
					$stmt->bind( 10, $row['costs'] );
					$stmt->bind( 11, $row['rebate'] );
					$stmt->bind( 12, $row['tax'] );
					$stmt->bind( 13, $row['taxrate'] );
					$stmt->bind( 14, $row['taxflag'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 15, $row['pos'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 16, $row['mtime'] );
					$stmt->bind( 17, $row['ctime'] );
					$stmt->bind( 18, $row['editor'] );
					$stmt->bind( 19, $row['id'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );

					$stmt->execute()->finish();
					$count++;
				}

				$start += $count;
			}
			while( $count === 1000 );

			$conn2->commit();

			$this->status( 'done' );
		}
		else
		{
			$this->status( 'skip' );
		}

		$this->release( $conn2, 'db-order' );
		$this->release( $conn, 'db-order' );
	}


	protected function updateServiceAttributes()
	{
		$this->msg( 'Copy mshop_order_base_service_attr to mshop_order_service_attr table', 1 );

		$conn = $this->acquire( 'db-order' );
		$conn2 = $this->acquire( 'db-order' );

		$select = 'SELECT * FROM "mshop_order_base_service_attr" LIMIT 1000 OFFSET :offset';
		$update = '
			INSERT "mshop_order_service_attr"
			SET "ordservid" = ?, "siteid" = ?, "attrid" = ?, "type" = ?, "code" = ?, "name" = ?,
				"value" = ?, "mtime" = ?, "ctime" = ?, "editor" = ?, "id" = ?
		';

		if( $conn->create( 'SELECT "id" FROM "mshop_order_service_attr" LIMIT 1' )->execute()->fetch() === false )
		{
			$stmt = $conn2->create( $update, \Aimeos\MW\DB\Connection\Base::TYPE_PREP );
			$start = 0;

			$conn2->begin();

			do
			{
				$count = 0;
				$result = $conn->create( str_replace( ':offset', $start, $select ) )->execute();

				while( ( $row = $result->fetch() ) !== false )
				{
					$stmt->bind( 1, $row['ordservid'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 2, $row['siteid'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 3, $row['attrid'] );
					$stmt->bind( 4, $row['type'] );
					$stmt->bind( 5, $row['code'] );
					$stmt->bind( 6, $row['name'] );
					$stmt->bind( 7, $row['value'] );
					$stmt->bind( 8, $row['mtime'] );
					$stmt->bind( 9, $row['ctime'] );
					$stmt->bind( 10, $row['editor'] );
					$stmt->bind( 11, $row['id'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );

					$stmt->execute()->finish();
					$count++;
				}

				$start += $count;
			}
			while( $count === 1000 );

			$conn2->commit();

			$this->status( 'done' );
		}
		else
		{
			$this->status( 'skip' );
		}

		$this->release( $conn2, 'db-order' );
		$this->release( $conn, 'db-order' );
	}
}
