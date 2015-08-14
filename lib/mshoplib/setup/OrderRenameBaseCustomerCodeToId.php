<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Renames column customercode to customerid in order base table.
 */
class MW_Setup_Task_OrderRenameBaseCustomerCodeToId extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_order_base' => 
			'ALTER TABLE "mshop_order_base" CHANGE "customercode" "customerid" VARCHAR(32) NOT NULL'
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
	 * Renames Attribute Customercode to Customerid if it exists.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Renaming order base customercode to customerid', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) && $this->_schema->columnExists( $table, 'customercode' ) === true )
			{
				$this->_execute( $stmt );
				$this->_status( 'renamed' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}	
}