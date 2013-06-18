<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Changes countryid column in customer table.
 */
class MW_Setup_Task_CustomerChangeCountryidNull extends MW_Setup_Task_Abstract
{
	private $_mysql = 'ALTER TABLE "mshop_customer" MODIFY "countryid" CHAR(2) NULL';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CustomerAddColumns' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
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
	 * Changes column in table.
	 *
	 * @param string $stmt SQL statement to execute for changing column
	 */
	protected function _process( $stmt )
	{
		$column = 'countryid';
		$table = 'mshop_customer';

		$this->_msg( sprintf( 'Allow NULL for "%2$s" in table "%1$s"', $table, $column ), 0 );

		if( $this->_schema->tableExists( $table )
			&& $this->_schema->columnExists( $table, $column ) === true
			&& $this->_schema->getColumnDetails( $table, $column )->isNullable() === false
		) {
			$this->_execute( $stmt );
			$this->_status( 'changed' );
		} else {
			$this->_status( 'OK' );
		}
	}
}
