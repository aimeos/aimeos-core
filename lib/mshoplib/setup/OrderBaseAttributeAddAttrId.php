<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds attrid column to mshop_order_base_*_attr tables.
 */
class OrderBaseAttributeAddAttrId extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_order_base_product_attr' => 'ALTER TABLE "mshop_order_base_product_attr" ADD "attrid" VARCHAR(32) NOT NULL COLLATE utf8_bin AFTER "siteid"',
		'mshop_order_base_service_attr' => 'ALTER TABLE "mshop_order_base_service_attr" ADD "attrid" VARCHAR(32) NOT NULL COLLATE utf8_bin AFTER "siteid"',
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
		$this->msg( 'Adding attrid column to order attribute tables', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true &&
				$this->schema->columnExists( $table, 'attrid' ) === false
			) {
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