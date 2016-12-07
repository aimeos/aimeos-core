<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Renames the "prodid" column to "parentid"
 */
class ProductChangeStockProductidParentid extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'prodid' => array(
			'ALTER TABLE "mshop_product_stock" DROP FOREIGN KEY "fk_msprost_prodid"',
			'ALTER TABLE "mshop_product_stock" CHANGE "prodid" "parentid" INTEGER NOT NULL',
			'ALTER TABLE "mshop_product_stock" ADD CONSTRAINT "fk_msprost_parentid" FOREIGN KEY ("parentid") REFERENCES "mshop_product" ("id") ON UPDATE CASCADE ON DELETE CASCADE',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductModifyIndexes', 'ProductStockExtendUniqueByWarehouseid' );
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
	 * Changes the column in table
	 *
	 * array string $stmts List of SQL statements for changing the columns
	 */
	protected function process( array $stmts )
	{
		$table = 'mshop_product_stock';
		$this->msg( sprintf( 'Rename "prodid" to "parentid" in table "%1$s"', $table ), 0 ); $this->status( '' );

		foreach( $stmts as $column => $stmts )
		{
			$this->msg( sprintf( 'Checking column "%1$s"', $column ), 1 );

			if( $this->schema->tableExists( $table )
				&& $this->schema->columnExists( $table, $column ) === true
			) {
				$this->executeList( $stmts );
				$this->status( 'done' );
			} else {
				$this->status( 'OK' );
			}
		}
	}
}
