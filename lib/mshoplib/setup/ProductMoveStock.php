<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Moves the stock tables to own domain
 */
class ProductMoveStock extends \Aimeos\MW\Setup\Task\Base
{
	private $stmts = array(
		'mshop_product_stock' => 'ALTER TABLE "mshop_product_stock" RENAME TO "mshop_stock"',
		'mshop_product_stock_type' => 'ALTER TABLE "mshop_product_stock_type" RENAME TO "mshop_stock_type"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductRenameStockWarehouse', 'ProductChangeStockProductidParentid' );
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
		$this->msg( 'Move stock tables to own domain', 0 ); $this->status( '' );
		$schema = $this->getSchema( 'db-product' );

		foreach( $this->stmts as $name => $stmt )
		{
			$this->msg( sprintf( 'Checking "%1$s"', $name ), 1 );

			if( $schema->tableExists( $name ) ) {
				$this->execute( $stmt );
				$this->status( 'done' );
			} else {
				$this->status( 'OK' );
			}
		}
	}
}
