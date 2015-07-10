<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Changes langid column in customer table.
 */
class MW_Setup_Task_CustomerChangeLangidLength extends MW_Setup_Task_Abstract
{
	private $_mysql = 'ALTER TABLE "mshop_customer" MODIFY "langid" VARCHAR(5) NULL';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CustomerAddColumns' );
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
	 * Changes column in table.
	 *
	 * @param string $stmt SQL statement to execute for changing column
	 */
	protected function _process( $stmt )
	{
		$column = 'langid';
		$table = 'mshop_customer';
		$this->_msg( sprintf( 'Changing length of "%2$s" in table "%1$s"', $table, $column ), 0 );

		if( $this->_schema->tableExists( $table )
			&& $this->_schema->columnExists( $table, $column ) === true
			&& $this->_schema->getColumnDetails( $table, $column )->getMaxLength() === 2
		) {
			$this->_execute( $stmt );
			$this->_status( 'changed' );
		} else {
			$this->_status( 'OK' );
		}
	}
}