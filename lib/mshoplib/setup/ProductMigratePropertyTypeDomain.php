<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes default product type and label values.
 */
class ProductMigratePropertyTypeDomain extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = 'UPDATE "mshop_product_property_type" SET "domain" = \'product\' WHERE "code" = \'product/property\'';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return [];
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
	 * Executes the task.
	 *
	 * @param string $stmt SQL statement to execute
	 */
	protected function process( $stmt )
	{
		$msg = 'Migrating product property domain to "product"';
		$this->msg( $msg, 0 );

		if( $this->schema->tableExists( 'mshop_product_property_type' ) )
		{
			$result = $this->conn->create( $stmt )->execute();
			$cntRows = $result->affectedRows();
			$result->finish();

			if( $cntRows ) {
				$this->status( sprintf( '%1$d/%1$d', $cntRows ) );
			} else {
				$this->status( 'OK' );
			}
		}
		else
		{
			$this->status( 'OK' );
		}
	}

}