<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Changes warehouse ID to NOT NULL in stock table.
 */
class MW_Setup_Task_ProductChangeStockWarehouseIdNotNull extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
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
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductWarehouseRenameTable', 'MShopAddWarehouseData' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$table = 'mshop_product_stock';
		$this->_msg( sprintf( 'Changing warehouseid column in %1$s', $table ), 0 );

		$schema = $this->_getSchema( 'db-product' );

		if( $schema->tableExists( $table ) === true
			&& $schema->columnExists( $table, 'warehouseid' )
			&& $schema->getColumnDetails( $table, 'warehouseid' )->isNullable() === true
		) {
			$this->_executeList( $this->_mysql, 'db-product' );
			$this->_status( 'done' );
		} else {
			$this->_status( 'OK' );
		}
	}
}