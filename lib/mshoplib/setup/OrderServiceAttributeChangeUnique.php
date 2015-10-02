<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


/**
 * Adds type column to unique index in order base service attribute table.
 */
class MW_Setup_Task_OrderServiceAttributeChangeUnique extends MW_Setup_Task_Abstract
{
	private $mysql = array(
		'unq_msordbaseat_ordservid_code' => 'DROP INDEX "unq_msordbaseat_ordservid_code" ON "mshop_order_base_service_attr"',
		'unq_msordbaseat_osid_type_code' => 'CREATE UNIQUE INDEX "unq_msordbaseat_osid_type_code" ON "mshop_order_base_service_attr" ("ordservid","type","code")',
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
		$status = 'OK';
		$table = 'mshop_order_base_service_attr';
		$this->msg( sprintf( 'Adding type to unique index to "%1$s"', $table ), 0 );

		if( $this->schema->tableExists( $table ) === true )
		{
			if( $this->schema->constraintExists( $table, 'unq_msordbaseat_osid_type_code' ) === false )
			{
				$this->execute( $stmts['unq_msordbaseat_osid_type_code'] );
				$status = 'done';
			}

			if( $this->schema->constraintExists( $table, 'unq_msordbaseat_ordservid_code' ) === true )
			{
				$this->execute( $stmts['unq_msordbaseat_ordservid_code'] );
				$status = 'done';
			}
		}

		$this->status( $status );
	}
}
