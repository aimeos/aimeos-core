<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes default product type and label values.
 */
class ProductChangeTypeCodeProductToDefault extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = 'UPDATE "mshop_product_type" SET "code" = \'default\', "label" = \'Article\' WHERE "code" = \'product\'';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TypesAddLabelStatus' );
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
		$msg = 'Changing code from "product" to "default" in "mshop_product_type"';
		$this->msg( $msg, 0 );

		if( $this->schema->tableExists( 'mshop_product_type' ) )
		{
			$result = $this->conn->create( $stmt )->execute();
			$cntRows = $result->affectedRows();
			$result->finish();

			if( $cntRows ) {
				$this->status( sprintf( 'migrated (%1$d)', $cntRows ) );
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