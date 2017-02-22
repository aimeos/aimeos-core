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
		'constraint' => 'ALTER TABLE "mshop_product_stock" DROP FOREIGN KEY "fk_msprost_whid"',
		'typeid' => 'ALTER TABLE "mshop_product_stock" CHANGE COLUMN "warehouseid" "typeid" INTEGER NOT NULL',
		'table' => 'ALTER TABLE "mshop_product_stock_warehouse" RENAME TO "mshop_product_stock_type"',
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
		$this->msg( 'Rename warehouse table', 0 ); $this->status( '' );

		$schema = $this->getSchema( 'db-product' );


		$this->msg( 'Drop "mshop_product_stock.fk_msprost_whid"', 1 );

		if( $schema->tableExists( 'mshop_product_stock' )
			&& $schema->constraintExists( 'mshop_product_stock', 'fk_msprost_whid' )
		) {
			$this->execute( $this->stmts['constraint'], 'db-product' );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}


		$this->msg( 'Rename "mshop_product_stock.wareshouseid"', 1 );

		if( $schema->tableExists( 'mshop_product_stock' )
			&& $schema->columnExists( 'mshop_product_stock', 'warehouseid' )
		) {
			$this->execute( $this->stmts['typeid'], 'db-product' );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}


		$this->msg( 'Rename "mshop_product_stock_wareshouse"', 1 );

		if( $schema->tableExists( 'mshop_product_stock_warehouse' ) )
		{
			$this->execute( $this->stmts['table'], 'db-product' );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}