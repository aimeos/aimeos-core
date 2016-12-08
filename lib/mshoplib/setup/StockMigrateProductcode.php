<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrates the product code column
 */
class StockMigrateProductcode extends \Aimeos\MW\Setup\Task\Base
{
	private $stmts = array(
		'DROP INDEX "unq_msprost_sid_pid_tid" ON "mshop_stock"',
		'ALTER TABLE "mshop_stock" ADD COLUMN "productcode" VARCHAR(32) NOT NULL',
		'UPDATE "mshop_stock" SET "productcode" = (
			SELECT "code" FROM "mshop_product" AS p WHERE p."id" = "parentid" AND p."siteid" = "siteid" LIMIT 1 OFFSET 0
		)',
		'ALTER TABLE "mshop_stock" DROP FOREIGN KEY "fk_msprost_pid"',
		'ALTER TABLE "mshop_stock" DROP COLUMN "parentid"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductMoveStock' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Creates the MShop tables
	 */
	public function migrate()
	{
		$this->msg( 'Migrate product code in stock table', 0 );
		$schema = $this->getSchema( 'db-product' );

		if( $schema->tableExists( 'mshop_stock' )
			&& $schema->columnExists( 'mshop_stock', 'productcode' ) === false
		) {
			$this->executeList( $this->stmts );
			$this->status( 'done' );
		} else {
			$this->status( 'OK' );
		}
	}
}
