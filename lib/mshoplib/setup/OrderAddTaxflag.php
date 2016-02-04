<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds tax flag for prices in order tables
 */
class OrderAddTaxflag extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_order_base' => 'ALTER TABLE "mshop_order_base" ADD "taxflag" SMALLINT NOT NULL DEFAULT 1 AFTER "tax"',
		'mshop_order_base_product' => 'ALTER TABLE "mshop_order_base_product" ADD "taxflag" SMALLINT NOT NULL DEFAULT 1 AFTER "taxrate"',
		'mshop_order_base_service' => 'ALTER TABLE "mshop_order_base_service" ADD "taxflag" SMALLINT NOT NULL DEFAULT 1 AFTER "taxrate"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables', 'OrderAddTax' );
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
		$this->msg( 'Adding tax flag to order tables', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true &&
				$this->schema->columnExists( $table, 'taxflag' ) === false )
			{
				$this->execute( $stmt );
				$this->status( 'added' );
			} else {
				$this->status( 'OK' );
			}
		}
	}
}