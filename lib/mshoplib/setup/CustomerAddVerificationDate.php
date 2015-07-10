<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds verification date column to customer table.
 */
class MW_Setup_Task_CustomerAddVerificationDate extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'ALTER TABLE "mshop_customer" ADD "vdate" DATE NULL AFTER "status"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array();
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array('TablesCreateMShop');
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
		$table = 'mshop_customer';
		$this->_msg( sprintf( 'Adding verification date column to table "%1$s"', $table ), 0 );

		if( $this->_schema->tableExists( $table )
			&& $this->_schema->columnExists( $table, 'vdate' ) === false )
		{
			$this->_executeList( $stmts );
			$this->_status( 'done' );
		} else {
			$this->_status( 'OK' );
		}
	}
}