<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrate the database schema
 */
class ProductChangeStockWarehouseCodeLength extends \Aimeos\MW\Setup\Task\Base
{
	private $list = array(
		'mysql' => 'ALTER TABLE "mshop_product_stock_warehouse" MODIFY "code" VARCHAR(32) NOT NULL',
		'pgsql' => 'ALTER TABLE "mshop_product_stock_warehouse" ALTER COLUMN "code" TYPE VARCHAR(32)',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array();
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Update database schema
	 */
	public function migrate()
	{
		$this->msg( 'Changing length of "mshop_product_stock_warehouse.code"', 0 );

		$schema = $this->getSchema( 'db-product' );

		if( isset( $this->list[$schema->getName()] )
			&& $schema->tableExists( 'mshop_product_stock_warehouse' ) === true
			&& $schema->columnExists( 'mshop_product_stock_warehouse', 'code' ) === true
			&& $schema->getColumnDetails( 'mshop_product_stock_warehouse', 'code' )->getMaxLength() > 32 )
		{
			$this->execute( $this->list[$schema->getName()] );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
