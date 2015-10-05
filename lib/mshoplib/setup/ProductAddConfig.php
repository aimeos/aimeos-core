<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds config column to product table.
 */
class ProductAddConfig extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = 'ALTER TABLE "mshop_product" ADD "config" TEXT NOT NULL AFTER "label"';


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
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMShop' );
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
	 * @param string $stmt SQL statement to execute for adding columns
	 */
	protected function process( $stmt )
	{
		$this->msg( 'Adding config column to mshop_product', 0 );

		if( $this->schema->tableExists( 'mshop_product' ) === true
			&& $this->schema->columnExists( 'mshop_product', 'config' ) === false )
		{
			$this->execute( $stmt );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}