<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2020
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes default product type and label values.
 */
class ProductMigratePropertyTypeDomain extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = 'UPDATE "mshop_product_property_type" SET "domain" = \'product\' WHERE "code" = \'product/property\'';


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	public function migrate()
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
			$conn = $this->acquire( 'db-product' );

			$result = $conn->create( $stmt )->execute();
			$cntRows = $result->affectedRows();
			$result->finish();

			$this->release( $conn, 'db-product' );

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
