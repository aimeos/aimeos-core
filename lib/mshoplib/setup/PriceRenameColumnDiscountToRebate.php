<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Renames discount column to rebate in price table.
 */
class PriceRenameColumnDiscountToRebate extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_price' => 'ALTER TABLE "mshop_price" CHANGE "discount" "rebate" DECIMAL(12,2) NOT NULL',
		'mshop_order_base' => 'ALTER TABLE "mshop_order_base" CHANGE "discount" "rebate" DECIMAL(12,2) NOT NULL',
		'mshop_order_base_product' => 'ALTER TABLE "mshop_order_base_product" CHANGE "discount" "rebate" DECIMAL(12,2) NOT NULL',
		'mshop_order_base_service' => 'ALTER TABLE "mshop_order_base_service" CHANGE "discount" "rebate" DECIMAL(12,2) NOT NULL',
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
		$this->msg( 'Renaming column "discount" to "rebate"', 0 ); $this->status( '' );

		foreach( $stmts as $table=>$stmt )
		{
			$this->msg( sprintf( 'Checking table "%1$s"', $table ), 1 );

			if( $this->schema->tableExists( $table ) && $this->schema->columnExists( $table, 'discount' ) === true )
			{
				$this->execute( $stmt );
				$this->status( 'renamed' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
