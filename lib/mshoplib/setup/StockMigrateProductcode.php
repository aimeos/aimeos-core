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
	private $indexes = array(
		'unq_msprost_sid_pid_tid' => 'DROP INDEX "unq_msprost_sid_pid_tid" ON "mshop_stock"',
		'unq_msprost_sid_pid_wid' => 'DROP INDEX "unq_msprost_sid_pid_wid" ON "mshop_stock"',
	);
	private $updates = array(
		'ALTER TABLE "mshop_stock" ADD COLUMN "productcode" VARCHAR(32) NOT NULL',
		'UPDATE "mshop_stock" SET "productcode" = (
			SELECT "code" FROM "mshop_product" AS p WHERE p."id" = "parentid" AND p."siteid" = "siteid" LIMIT 1 OFFSET 0
		)',
	);
	private $constraints = array(
		'fk_msprost_parentid' => 'ALTER TABLE "mshop_stock" DROP FOREIGN KEY "fk_msprost_parentid"',
		'fk_msprost_pid' => 'ALTER TABLE "mshop_stock" DROP FOREIGN KEY "fk_msprost_pid"',
	);
	private $column = 'ALTER TABLE "mshop_stock" DROP COLUMN "parentid"';


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


			$this->msg( 'Migrate to column "productcode"', 1 );

			if( $schema->columnExists( 'mshop_stock', 'productcode' ) === false )
			{
				$this->executeList( $this->updates );
				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}


			foreach( $this->constraints as $name => $stmt )
			{
				$this->msg( sprintf( 'Remove foreign key "%1$s"', $name ), 1 );

				if( $schema->constraintExists( 'mshop_stock', $name ) === true )
				{
					$this->execute( $stmt );
					$this->status( 'done' );
				}
				else
				{
					$this->status( 'OK' );
				}
			}

			$this->msg( 'Remove column "parentid"', 1 );

			if( $schema->columnExists( 'mshop_stock', 'parentid' ) === true )
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
