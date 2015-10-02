<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Changes default value of mtime column in order tables.
 */
class MW_Setup_Task_OrderChangeColumnMTimeDefaultValue extends MW_Setup_Task_Abstract
{
	private $mysql = array(
		'mshop_order_base' => 'ALTER TABLE "mshop_order_base" CHANGE "mtime" "mtime" DATETIME NOT NULL',
		'mshop_order' => 'ALTER TABLE "mshop_order" CHANGE "mtime" "mtime" DATETIME NOT NULL',
		'mshop_order_base_address' => 'ALTER TABLE "mshop_order_base_address" CHANGE "mtime" "mtime" DATETIME NOT NULL',
		'mshop_order_base_product' => 'ALTER TABLE "mshop_order_base_product" CHANGE "mtime" "mtime" DATETIME NOT NULL',
		'mshop_order_base_product_attr' => 'ALTER TABLE "mshop_order_base_product_attr" CHANGE "mtime" "mtime" DATETIME NOT NULL',
		'mshop_order_base_coupon' => 'ALTER TABLE "mshop_order_base_coupon" CHANGE "mtime" "mtime" DATETIME NOT NULL',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables', 'OrderAddComment' );
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
		$this->msg( 'Changing default value from column "mtime" to "NOT NULL"', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->msg( sprintf( 'Checking table "%1$s"', $table ), 1 );

			if( $this->schema->tableExists( $table ) && $this->schema->columnExists( $table, 'mtime' ) === true && $this->schema->getColumnDetails( $table, 'mtime' )->isNullable() )
			{
				$this->execute( $stmt );
				$this->status( 'changed' );
			}
			else {
				$this->status( 'OK' );
			}
		}
	}
}
