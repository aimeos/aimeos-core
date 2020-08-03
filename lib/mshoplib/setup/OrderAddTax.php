<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2020
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds tax value for prices in order tables
 */
class OrderAddTax extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_order_base_product' => array(
			'ALTER TABLE "mshop_order_base_product" ADD "tax" DECIMAL(14,4) NOT NULL AFTER "rebate"',
			'UPDATE "mshop_order_base_product" SET "tax" = ROUND(("price" + "costs") / (100 + "taxrate") * "taxrate", 4)',
		),
		'mshop_order_base_service' => array(
			'ALTER TABLE "mshop_order_base_service" ADD "tax" DECIMAL(14,4) NOT NULL AFTER "rebate"',
			'UPDATE "mshop_order_base_service" SET "tax" = ROUND(("price" + "costs") / (100 + "taxrate") * "taxrate", 4)',
		),
		'mshop_order_base' => array(
			'ALTER TABLE "mshop_order_base" ADD "tax" DECIMAL(14,4) NOT NULL AFTER "rebate"',
			'UPDATE "mshop_order_base" b SET b."tax" = ( SELECT SUM(p."tax") FROM "mshop_order_base_product" AS p WHERE p."baseid" = b."id" )',
			'UPDATE "mshop_order_base" b SET b."tax" = b."tax" + ( SELECT SUM(s."tax") FROM "mshop_order_base_service" AS s WHERE s."baseid" = b."id" )',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['OrderRenameTables'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	public function migrate()
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
		$this->msg( 'Adding tax column to order tables', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true &&
				$this->schema->columnExists( $table, 'tax' ) === false )
			{
				$this->executeList( $stmt );
				$this->status( 'added' );
			} else {
				$this->status( 'OK' );
			}
		}
	}
}
