<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2020
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds domain values to the stock type table
 */
class ProductAddStockTypeDomainValues extends \Aimeos\MW\Setup\Task\Base
{
	private $stmt = 'UPDATE "mshop_product_stock_type" SET "domain"=\'product\' WHERE "domain"=\'\'';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Creates the MShop tables
	 */
	public function migrate()
	{
		$this->msg( 'Add domain values to "mshop_product_stock_type" table', 0 );

		$schema = $this->getSchema( 'db-product' );

		if( $schema->tableExists( 'mshop_product_stock_type' ) )
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
