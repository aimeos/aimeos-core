<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrate the database schema
 */
class WarehouseDropIndexes extends \Aimeos\MW\Setup\Task\Base
{
	private $list = array(
		'idx_msprostwa_sid_ctime' => array(
			'mysql' => 'DROP INDEX "idx_msprostwa_sid_ctime" ON "mshop_product_stock_warehouse"',
			'pgsql' => 'DROP INDEX "idx_msprostwa_sid_ctime"',
		),
		'idx_msprostwa_sid_mtime' => array(
			'mysql' => 'DROP INDEX "idx_msprostwa_sid_mtime" ON "mshop_product_stock_warehouse"',
			'pgsql' => 'DROP INDEX "idx_msprostwa_sid_mtime"',
		),
		'idx_msprostwa_sid_editor' => array(
			'mysql' => 'DROP INDEX "idx_msprostwa_sid_editor" ON "mshop_product_stock_warehouse"',
			'pgsql' => 'DROP INDEX "idx_msprostwa_sid_editor"',
		),
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
		$this->clean();
	}


	/**
	 * Clean up database schema
	 */
	public function clean()
	{
		$this->msg( 'Dropping indexes in "mshop_product_stock_warehouse"', 0 );
		$this->status( '' );

		$schema = $this->getSchema( 'db-product' );

		foreach( $this->list as $idx => $stmts )
		{
			$this->msg( sprintf( 'Dropping index "%1$s"', $idx ), 1 );

			if( isset( $stmts[$schema->getName()] )
				&& $schema->tableExists( 'mshop_product_stock_warehouse' ) === true
				&& $schema->indexExists( 'mshop_product_stock_warehouse', $idx ) === true )
			{
				$this->execute( $stmts[$schema->getName()] );
				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
