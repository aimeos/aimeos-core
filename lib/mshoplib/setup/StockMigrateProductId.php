<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrates product code to ID
 */
class StockMigrateProductId extends \Aimeos\MW\Setup\Task\Base
{
	private $indexes = array(
		'unq_mssto_sid_pcode_ty' => 'DROP INDEX "unq_mssto_sid_pcode_ty" ON "mshop_stock"',
	);
	private $updates = array(
		'ALTER TABLE "mshop_stock" ADD COLUMN "prodid" VARCHAR(36) NOT NULL',
		'UPDATE "mshop_stock" SET "prodid" = (
			SELECT "id" FROM "mshop_product" AS p WHERE p."code" = "productcode" AND p."siteid" = "siteid"
		)',
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
		$this->msg( 'Migrate product code in stock table', 0 ); $this->status( '' );
		$schema = $this->getSchema( 'db-product' );

		if( $schema->tableExists( 'mshop_stock' ) )
		{
			foreach( $this->indexes as $name => $stmt )
			{
				$this->msg( sprintf( 'Remove index "%1$s"', $name ), 1 );

				if( $schema->indexExists( 'mshop_stock', $name ) === true )
				{
					$this->execute( $stmt );
					$this->status( 'done' );
				}
				else
				{
					$this->status( 'OK' );
				}
			}


			$this->msg( 'Migrate to column "prodid"', 1 );

			if( $schema->columnExists( 'mshop_stock', 'prodid' ) === false )
			{
				$this->executeList( $this->updates );
				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}

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
	}
}
