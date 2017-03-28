<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes warehouse ID to NOT NULL in stock table.
 */
class ProductChangeStockWarehouseIdNotNull extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'UPDATE "mshop_product_stock" st
			SET "warehouseid" = ( SELECT "id" FROM "mshop_product_stock_warehouse" wh WHERE wh."siteid" = st."siteid" AND wh."code" = \'default\' )
			WHERE "warehouseid" IS NULL',
		'ALTER TABLE "mshop_product_stock"
			DROP FOREIGN KEY "fk_msprost_stock_warehouseid",
			MODIFY "warehouseid" INTEGER NOT NULL',
		'ALTER TABLE "mshop_product_stock"
			ADD CONSTRAINT "fk_msprost_stock_warehouseid"
				FOREIGN KEY ("warehouseid")
				REFERENCES "mshop_product_stock_warehouse" ("id")
				ON UPDATE CASCADE
				ON DELETE CASCADE',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductWarehouseRenameTable', 'MShopAddWarehouseData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
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
		$table = 'mshop_product_stock';
		$this->msg( sprintf( 'Changing warehouseid column in %1$s', $table ), 0 );

		$schema = $this->getSchema( 'db-product' );

		if( $schema->tableExists( $table ) === true
			&& $schema->columnExists( $table, 'warehouseid' )
			&& $schema->getColumnDetails( $table, 'warehouseid' )->isNullable() === true
		) {
			$this->executeList( $this->mysql, 'db-product' );
			$this->status( 'done' );
		} else {
			$this->status( 'OK' );
		}
	}
}