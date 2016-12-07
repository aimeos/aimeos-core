<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds primary key to order base service attribute table.
 */
class OrderServiceAttributeAddPrimaryKey extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'CREATE TEMPORARY TABLE "mshop_order_base_service_attr_temp" LIKE "mshop_order_base_service_attr"',
		'INSERT INTO "mshop_order_base_service_attr_temp" ("ordservid", "code", "value", "mtime")
			SELECT "ordservid", "code", "value", "mtime" FROM "mshop_order_base_service_attr"',
		'TRUNCATE TABLE "mshop_order_base_service_attr"',
		'ALTER TABLE "mshop_order_base_service_attr" ADD "id" INTEGER NOT NULL FIRST',
		'ALTER TABLE "mshop_order_base_service_attr" ADD CONSTRAINT "pk_msordbaseat_id" PRIMARY KEY ("id")',
		'ALTER TABLE "mshop_order_base_service_attr" CHANGE "id" "id" INTEGER NOT NULL AUTO_INCREMENT',
		'INSERT INTO "mshop_order_base_service_attr" ("ordservid", "code", "value", "mtime")
			SELECT "ordservid", "code", "value", "mtime" FROM "mshop_order_base_service_attr_temp"',
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
	 * Migrates service attribute data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Adding id to order service attribute table', 0 ); $this->status( '' );
		$this->msg( 'Checking column "id":', 1 );

		if( $this->schema->tableExists( 'mshop_order_base_service_attr' ) === true
			&& $this->schema->columnExists( 'mshop_order_base_service_attr', 'id' ) === false )
		{
			$this->executeList( $stmts );
			$this->status( 'added' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
