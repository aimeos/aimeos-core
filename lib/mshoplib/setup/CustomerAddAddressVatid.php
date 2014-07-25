<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds vatid column to address tables.
 */
class MW_Setup_Task_CustomerAddAddressVatid extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_customer' => 'ALTER TABLE "mshop_customer" ADD "vatid" VARCHAR(32) AFTER "company"',
		'mshop_customer_address' => 'ALTER TABLE "mshop_customer_address" ADD "vatid" VARCHAR(32) AFTER "company"',
	);




	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array('CustomerAddColumns');
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
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
		$this->_msg( 'Adding "vatid" column to customer address tables', 0 ); $this->_status( '' );

		foreach( $stmts AS $table => $stmt )
		{
			$this->_msg( sprintf( 'Checking "%1$s" table', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true
				&& $this->_schema->columnExists( $table, 'vatid' ) === false )
			{
				$this->_execute( $stmt );
				$this->_status( 'added' );
			} else {
				$this->_status( 'OK' );
			}
		}
	}
}