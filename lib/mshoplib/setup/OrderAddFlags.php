<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds flag and emailflag columns in order table.
 *
 * 2013-08-01: flag column was removed in favour of entries in mshop_order_status
 */
class MW_Setup_Task_OrderAddFlags extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'flag' => 'ALTER TABLE "mshop_order" ADD "flag" SMALLINT NOT NULL AFTER "dstatus"',
		'emailflag' => 'ALTER TABLE "mshop_order" ADD "emailflag" SMALLINT NOT NULL AFTER "flag"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderAlterForeignKeyContraintsOnDelete' );
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
		$this->_msg( 'Adding flag and emailflag column to order table', 0 ); $this->_status( '' );

		// $this->_process( $this->_mysql );
	}


	/**
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$table = "mshop_order";
		$this->_msg(sprintf('Adding columns to table "%1$s"', $table), 0);
		$this->_status('');

		foreach ( $stmts AS $column => $stmt ) {
			$this->_msg(sprintf('Checking column "%1$s": ', $column), 1);

			if ( $this->_schema->tableExists($table) === true &&
				$this->_schema->columnExists($table, $column) === false ) {
				$this->_execute($stmt);
				$this->_status('added');
			} else {
				$this->_status('OK');
			}
		}
	}
}