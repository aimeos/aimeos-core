<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds column type to tables mshop_order_base_product_attr and mshop_order_base_service_attr.
 */
class OrderAddBaseAttrAddType extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_order_base_product_attr' => 'ALTER TABLE "mshop_order_base_product_attr" ADD "type" VARCHAR(32) NOT NULL AFTER "ordprodid"',
		'mshop_order_base_service_attr' => 'ALTER TABLE "mshop_order_base_service_attr" ADD "type" VARCHAR(32) NOT NULL AFTER "ordservid"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTable' );
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
	 * @param array $stmts Associative list of table names and SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Add column type to order attribute tables', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->msg( sprintf( 'Checking "%1$s" table', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true
				&& $this->schema->columnExists( $table, 'type' ) === false )
			{
				$this->execute( $stmt );
				$this->status( 'added' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
