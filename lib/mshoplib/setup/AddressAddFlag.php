<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds flag column to address tables.
 */
class MW_Setup_Task_AddressAddFlag extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_customer_address' => 'ALTER TABLE "mshop_customer_address" ADD "flag" INTEGER NOT NULL AFTER "website"',
		'mshop_supplier_address' => 'ALTER TABLE "mshop_supplier_address" ADD "flag" INTEGER NOT NULL AFTER "website"',
		'mshop_order_base_address' => 'ALTER TABLE "mshop_order_base_address" ADD "flag" INTEGER NOT NULL AFTER "website"',
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
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Adding "flag" column to address tables', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->_msg( sprintf( 'Checking "%1$s" table', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true
				&& $this->_schema->columnExists( $table, 'flag' ) === false )
			{
				$this->_execute( $stmt );
				$this->_status( 'added' );
			} else {
				$this->_status( 'OK' );
			}
		}
	}
}