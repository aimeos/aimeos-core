<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrates the tax rate in order tables
 */
class OrderMigrateTaxrate extends \Aimeos\MW\Setup\Task\Base
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
		$this->msg( 'Migrating taxrate column in order tables', 0 ); $this->status( '' );

		if( $this->getSchema( $dbdomain )->tableExists( 'mshop_order_base_product' ) === true )
		{
			$this->msg( 'Migrating taxrate column in order base product table', 1 );

			$start = 0;
			$conn = $this->acquire( $dbdomain );
			$select = 'SELECT "id", "taxrate" FROM "mshop_order_base_product" WHERE "taxrate" NOT LIKE \'{%\' LIMIT 1000 OFFSET :offset';
			$update = 'UPDATE "mshop_order_base_product" SET "taxrate" = ? WHERE "id" = ?';

			$stmt = $conn->create( $update );

			do
			{
				$count = 0;
				$map = [];
				$sql = str_replace( ':offset', $start, $select );
				$result = $conn->create( $sql )->execute();

				while( ( $row = $result->fetch() ) !== false )
				{
					$map[$row['id']] = $row['taxrate'];
					$count++;
				}

				foreach( $map as $id => $taxrate )
				{
					$stmt->bind( 1, json_encode( ['' => $taxrate], JSON_FORCE_OBJECT ) );
					$stmt->bind( 2, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );

					$stmt->execute()->finish();
				}

				$start += $count;
			}
			while( $count === 1000 );

			$this->release( $conn, $dbdomain );

			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}


		if( $this->getSchema( $dbdomain )->tableExists( 'mshop_order_base_service' ) === true )
		{
			$this->msg( 'Migrating taxrate column in order base service table', 1 );

			$start = 0;
			$conn = $this->acquire( $dbdomain );
			$select = 'SELECT "id", "taxrate" FROM "mshop_order_base_service" WHERE "taxrate" NOT LIKE \'{%\' LIMIT 1000 OFFSET :offset';
			$update = 'UPDATE "mshop_order_base_service" SET "taxrate" = ? WHERE "id" = ?';

			$stmt = $conn->create( $update );

			do
			{
				$count = 0;
				$map = [];
				$sql = str_replace( ':offset', $start, $select );
				$result = $conn->create( $sql )->execute();

				while( ( $row = $result->fetch() ) !== false )
				{
					$map[$row['id']] = $row['taxrate'];
					$count++;
				}

				foreach( $map as $id => $taxrate )
				{
					$stmt->bind( 1, json_encode( ['' => $taxrate], JSON_FORCE_OBJECT ) );
					$stmt->bind( 2, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );

					$stmt->execute()->finish();
				}

				$start += $count;
			}
			while( $count === 1000 );

			$this->release( $conn, $dbdomain );

			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
