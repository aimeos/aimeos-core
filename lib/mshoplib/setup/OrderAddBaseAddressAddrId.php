<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds addrid column in order base address table.
 */
class MW_Setup_Task_OrderAddBaseAddressAddrId extends MW_Setup_Task_Base
{
	private $mysql = 'ALTER TABLE "mshop_order_base_address" ADD "addrid" VARCHAR(32) NOT NULL AFTER "siteid"';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderAddSiteId' );
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
		$this->msg( 'Adding addrid column to order address table', 0 ); $this->status( '' );

		$this->process( $this->mysql );
	}

	/**
	 * Add columns to tables if they doesn't exist.
	 *
	 * @param string $stmt List of SQL statements to execute for adding columns
	 */
	protected function process( $stmt )
	{
		$table = 'mshop_order_base_address';
		$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

		if( $this->schema->tableExists( $table ) === true
			&& $this->schema->columnExists( $table, 'addrid' ) === false )
		{
			$this->execute( $stmt );
			$this->status( 'added' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}