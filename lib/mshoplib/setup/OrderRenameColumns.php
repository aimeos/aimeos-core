<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id$
 */


/**
 * Renames columns of order table.
 */
class MW_Setup_Task_OrderRenameColumns extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'ddate' => 'ALTER TABLE "mshop_order" CHANGE "ddate" "datedelivery" DATETIME DEFAULT NULL',
		'pdate' => 'ALTER TABLE "mshop_order" CHANGE "pdate" "datepayment" DATETIME NOT NULL',
		'pstatus' => 'ALTER TABLE "mshop_order" CHANGE "pstatus" "statuspayment" SMALLINT NOT NULL DEFAULT -1',
		'dstatus' => 'ALTER TABLE "mshop_order" CHANGE "dstatus" "statusdelivery" SMALLINT NOT NULL DEFAULT -1',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array('OrderRenameConstraints');
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
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Renaming order columns pdate,ddate,dstatus,pstatus', 0 );
		$this->_status( '' );

		foreach( $stmts as $col => $stmt )
		{
			$this->_msg( sprintf( 'Checking columne "%1$s": ', $col ), 1 );

			if( $this->_schema->columnExists( 'mshop_order', $col ) )
			{
				$this->_execute( $stmt );
				$this->_status( 'renamed' );
			} else {
				$this->_status( 'OK' );
			}
		}
	}

}
