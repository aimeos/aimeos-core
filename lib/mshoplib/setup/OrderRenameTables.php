<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Move order_* tables to order_base_*.
 */
class OrderRenameTables extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_order_address' => array(
			'RENAME TABLE "mshop_order_address" TO "mshop_order_base_address"',
		),
		'mshop_order_discount' => array(
			'RENAME TABLE "mshop_order_discount" TO "mshop_order_base_discount"',
		),
		'mshop_order_product' => array(
			'RENAME TABLE "mshop_order_product" TO "mshop_order_base_product"',
		),
		'mshop_order_service' => array(
			'RENAME TABLE "mshop_order_service" TO "mshop_order_base_service"',
		),
		'mshop_order_service_attribute' => array(
			'RENAME TABLE "mshop_order_service_attribute" TO "mshop_order_base_service_attr"',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
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
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Renaming order tables', 0 ); $this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true )
			{
				$this->executeList( $stmtList );
				$this->status( 'renamed' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
