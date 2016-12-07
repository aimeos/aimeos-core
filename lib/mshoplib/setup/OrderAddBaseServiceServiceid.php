<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds order ID column to order base service tables.
 */
class OrderAddBaseServiceServiceid extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'ALTER TABLE "mshop_order_base_service" ADD "servid" VARCHAR(32) NOT NULL COLLATE utf8_bin AFTER "siteid"',
		'UPDATE "mshop_order_base_service" o SET "servid" = ( SELECT s."id" FROM "mshop_service" s WHERE s."siteid" = o."siteid" AND s."code" = o."code" LIMIT 1 ) WHERE "servid" = \'\'',
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
		$this->msg( 'Adding service ID to order base service table', 0 );
		$this->status( '' );

		$table = 'mshop_order_base_service';

		$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

		if( $this->schema->tableExists( $table ) === true &&
			$this->schema->columnExists( $table, 'servid' ) === false )
		{
			$this->executeList( $stmts );
			$this->status( 'added' );
		} else {
			$this->status( 'OK' );
		}
	}
}