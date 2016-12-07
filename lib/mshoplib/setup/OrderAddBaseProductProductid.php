<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds product ID column to order base product tables.
 */
class OrderAddBaseProductProductid extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'ALTER TABLE "mshop_order_base_product" ADD "prodid" VARCHAR(32) NOT NULL COLLATE utf8_bin AFTER "siteid"',
		'UPDATE "mshop_order_base_product" SET "prodid" = ( SELECT p."id" FROM "mshop_product" p WHERE p."siteid" = "siteid" AND p."code" = "prodcode" LIMIT 1 ) WHERE "prodid" = \'\'',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables', 'OrderAddSiteId' );
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
		$this->msg( 'Adding product ID to order base product table', 0 );
		$this->status( '' );

		$table = 'mshop_order_base_product';

		$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

		if( $this->schema->tableExists( $table ) === true &&
			$this->schema->columnExists( $table, 'prodid' ) === false )
		{
			$this->executeList( $stmts );
			$this->status( 'added' );
		} else {
			$this->status( 'OK' );
		}
	}
}