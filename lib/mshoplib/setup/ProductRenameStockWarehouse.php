<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Renames the stock warehouse table to type
 */
class ProductRenameStockWarehouse extends \Aimeos\MW\Setup\Task\Base
{
	private $stmts = array(
		'table' => 'ALTER TABLE "mshop_product_stock_warehouse" RENAME TO "mshop_product_stock_type"',
		'typeid' => 'ALTER TABLE "mshop_product_stock" CHANGE COLUMN "warehouseid" "typeid" INTEGER NOT NULL',
		'constraint' => 'ALTER TABLE "mshop_product_stock" DROP CONSTRAINT "fk_msprost_stock_warehouseid"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductChangeStockWarehouseIdNotNull', 'ProductWarehouseRenameTable', 'ProductWarehouseAddLabelStatus', 'TablesAddLogColumns', 'TablesChangeSiteidNotNull' );
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
		$this->msg( 'Rename warehouse table', 0 ); $this->status( '' );

		$schema = $this->getSchema( 'db-product' );


		$this->msg( 'Rename "mshop_product_stock_wareshouse"', 0 );

		if( $schema->tableExists( 'mshop_product_stock_warehouse' ) )
		{
			$this->execute( $this->stmt['table'], 'db-product' );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}


		$this->msg( 'Rename "mshop_product_stock.wareshouseid"', 0 );

		if( $schema->tableExists( 'mshop_product_stock' )
			&& $schema->columnExists( 'mshop_product_stock', 'warehouseid' )
		) {
			$this->execute( $this->stmt['typeid'], 'db-product' );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}


		$this->msg( 'Drop "mshop_product_stock.fk_msprost_stock_warehouseid"', 0 );

		if( $schema->tableExists( 'mshop_product_stock' )
			&& $schema->constraintExists( 'mshop_product_stock', 'fk_msprost_stock_warehouseid' )
		) {
			$this->execute( $this->stmt['constraint'], 'db-product' );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}