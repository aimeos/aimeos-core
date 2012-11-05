<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: OrderAddComment.php 14251 2011-12-09 13:36:27Z nsendetzky $
 */


/**
 * Adds comment column to order base table.
 */
class MW_Setup_Task_OrderAddComment extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'comment' => 'ALTER TABLE "mshop_order_base" ADD "comment" TEXT NOT NULL DEFAULT \'\' AFTER "discount"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables' );
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
		$this->_process($this->_mysql);
	}


	/**
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$table = "mshop_order_base";
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
