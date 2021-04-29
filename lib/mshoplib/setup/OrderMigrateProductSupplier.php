<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrate supplier code to ID and name in order product table
 */
class OrderMigrateProductSupplier extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Migrate database schema
	 */
	public function migrate()
	{
		$this->msg( 'Migrate supplier code to ID and name in order product table', 0 );

		$schema = $this->getSchema( 'db-order' );

		if( $schema->tableExists( 'mshop_order_base_product' )
			&& $schema->columnExists( 'mshop_order_base_product', 'suppliercode' )
		) {
			$this->addColumns();

			$conn = $this->acquire( 'db-order' );
			$sconn = $this->acquire( 'db-supplier' );

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

			$this->release( $sconn, 'db-supplier' );
			$this->release( $conn, 'db-order' );

			return $this->status( 'done' );
		}

		$this->status( 'OK' );
	}


	protected function addColumns()
	{
		$conn = $this->acquire( 'db-order' );
		$dbal = $conn->getRawObject();
		$this->release( $conn, 'db-order' );

		if( !( $dbal instanceof \Doctrine\DBAL\Connection ) ) {
			throw new \Aimeos\MW\Setup\Exception( 'Not a DBAL connection' );
		}

		$dbalManager = $dbal->getSchemaManager();
		$config = $dbalManager->createSchemaConfig();
		$platform = $dbal->getDatabasePlatform();

		$table = $dbalManager->listTableDetails( 'mshop_order_base_product' );
		$schema = new \Doctrine\DBAL\Schema\Schema( [clone $table], [], $config );

		if( !$table->hasColumn( 'supplierid' ) ) {
			$table->addColumn( 'supplierid', 'string', ['length' => 36, 'customSchemaOptions' => ['charset' => 'binary', 'default' => '']] );
		}

		if( !$table->hasColumn( 'suppliername' ) ) {
			$table->addColumn( 'suppliername', 'string', ['length' => 255, 'default' => ''] );
		}

		$newSchema = new \Doctrine\DBAL\Schema\Schema( [$table], [], $config );
		$stmts = \Doctrine\DBAL\Schema\Comparator::compareSchemas( $schema, $newSchema )->toSaveSql( $platform );

		$this->executeList( $stmts, 'db-order' );
	}
}
