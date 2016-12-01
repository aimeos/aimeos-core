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
	private $stmt = 'ALTER TABLE "mshop_product_stock_warehouse" RENAME TO "mshop_product_stock_type"';


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
		$this->msg( 'Rename "mshop_product_stock_wareshouse" table', 0 );

		$schema = $this->getSchema( 'db-product' );

		if( $schema->tableExists( 'mshop_product_stock_warehouse' ) )
		{
			$this->execute( $this->stmt, 'db-product' );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}