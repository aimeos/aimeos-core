<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds the domain value
 */
class StockAddTypeDomainValue extends \Aimeos\MW\Setup\Task\Base
{
	private $sql = 'UPDATE "mshop_stock_type" SET "domain"=\'product\' WHERE "domain"=\'\'';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop' );
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
	 * Cleans up the tables
	 */
	public function clean()
	{
		$this->migrate();
	}


	/**
	 * Migrate the tables
	 */
	public function migrate()
	{
		$this->msg( 'Add stock type domain values', 0 );
		$schema = $this->getSchema( 'db-product' );

		if( $schema->tableExists( 'mshop_stock_type' ) )
		{
			$this->execute( $this->sql );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
