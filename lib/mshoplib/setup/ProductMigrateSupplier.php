<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrates the suppliercode column to list references
 */
class ProductMigrateSupplier extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'INSERT INTO "mshop_product_list" ("parentid", "siteid", "typeid", "domain", "refid", "config", "status", "ctime", "mtime", "editor")
		 SELECT p."id", p."siteid", (SELECT t."id" FROM "mshop_product_list_type" t WHERE t."siteid" = p."siteid" AND t."domain" = \'supplier\' AND t."code" = \'default\'), \'supplier\', s."id", \'[]\', 1, NOW(), NOW(), \'setup:ProductMigrateSupplier\'
		 FROM "mshop_supplier" s JOIN "mshop_product" p ON p."suppliercode" = s."code"',
		'ALTER TABLE "mshop_product" DROP INDEX "idx_mspro_sid_supplier"',
		'ALTER TABLE "mshop_product" DROP COLUMN "suppliercode"'
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductPackagesToProducts', 'MShopAddTypeData' );
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
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process( $this->mysql );
	}

	/**
	 * Add column to table if it doesn't exist.
	 *
	 * @param array $stmts SQL statement to execute for adding columns
	 */
	protected function process( $stmts )
	{
		$this->msg( 'Migrate suppliercode in mshop_product', 0 );

		if( $this->schema->tableExists( 'mshop_product' ) === true
			&& $this->schema->columnExists( 'mshop_product', 'suppliercode' ) === true )
		{
			$this->executeList( $stmts );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}