<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds vatid column to address tables.
 */
class MW_Setup_Task_OrderAddBaseAddressVatid extends MW_Setup_Task_Abstract
{
	private $mysql = array(
		'mshop_order_base_address' => 'ALTER TABLE "mshop_order_base_address" ADD "vatid" VARCHAR(32) AFTER "company"',
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
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Adding "vatid" column to order base address tables', 0 ); $this->status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->msg( sprintf( 'Checking "%1$s" table', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true
				&& $this->schema->columnExists( $table, 'vatid' ) === false )
			{
				$this->execute( $stmt );
				$this->status( 'added' );
			} else {
				$this->status( 'OK' );
			}
		}
	}
}