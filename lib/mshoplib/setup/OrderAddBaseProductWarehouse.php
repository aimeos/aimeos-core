<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds warehouse column to order base product tables.
 */
class OrderAddBaseProductWarehouse extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'UPDATE "mshop_order_base_product" SET "stocktype" = \'default\' WHERE "stocktype" = \'\'',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables' );
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
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Adding warehouse code to order base product table', 0 );

		$table = 'mshop_order_base_product';
		$schema = $this->getSchema( 'db-order' );

		if( $schema->tableExists( $table ) === true &&
			$schema->columnExists( $table, 'stocktype' ) === true )
		{
			$this->executeList( $stmts, 'db-order' );
			$this->status( 'done' );
		} else {
			$this->status( 'OK' );
		}
	}
}