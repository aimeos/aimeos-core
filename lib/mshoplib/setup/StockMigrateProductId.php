<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrates product code to ID
 */
class StockMigrateProductId extends \Aimeos\MW\Setup\Task\Base
{
	private $updates = array(
		'ALTER TABLE "mshop_stock" ADD COLUMN "prodid" VARCHAR(36)',
		'UPDATE "mshop_stock" SET "prodid" = (
			SELECT "id" FROM "mshop_product" AS p WHERE p."code" = "productcode" AND p."siteid" = "siteid" LIMIT 1
		)'
	);
	private $column = 'ALTER TABLE "mshop_stock" DROP COLUMN "productcode"';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['StockMigrateProductcode'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Cleans up the tables
	 */
	public function clean()
	{
		$this->migrate();
	}


	/**
	 * Creates the MShop tables
	 */
	public function migrate()
	{
		$this->msg( 'Migrate product code in stock table', 0, '' );

		$rname = 'db-product';
		$table = 'mshop_stock';

		$schema = $this->getSchema( $rname );
		$conn = $this->acquire( $rname );
		$dbal = $conn->getRawObject();

		if( !( $dbal instanceof \Doctrine\DBAL\Connection ) ) {
			throw new \Aimeos\MW\Setup\Exception( 'Not a DBAL connection' );
		}

		$dbalManager = $dbal->getSchemaManager();

		if( $schema->tableExists( 'mshop_stock' ) )
		{
			$this->msg( 'Migrate "productcode" to "prodid"', 1 );

			if( $schema->columnExists( 'mshop_stock', 'prodid' ) === false )
			{
				$this->executeList( $this->updates );
				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}

			$this->execute( 'DELETE FROM "mshop_stock" WHERE "prodid" IS NULL' );
			$dbalManager->tryMethod( 'dropIndex', 'unq_mssto_sid_pcode_ty', 'mshop_stock' );

			$this->msg( 'Remove column "productcode"', 1 );

			if( $schema->columnExists( 'mshop_stock', 'productcode' ) === true )
			{
				$this->execute( $this->column );
				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}

		$this->release( $conn, $rname );
	}
}
