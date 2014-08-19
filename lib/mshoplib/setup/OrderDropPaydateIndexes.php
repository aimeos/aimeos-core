<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Drops the old *_pdate_* indexes in the order tables.
 */
class MW_Setup_Task_OrderDropPaydateIndexes extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'idx_msord_sid_pdate_pstat_dstat' => 'ALTER TABLE "mshop_order" DROP INDEX "idx_msord_sid_pdate_pstat_dstat"',
		'idx_msord_sid_mtime' => 'ALTER TABLE "mshop_order" DROP INDEX "idx_msord_sid_mtime"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array();
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
	 * Adds and modifies indexes in the mshop_catalog table.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Drop old pay date indexes in mshop_order table', 0 );
		$this->_status( '' );

		foreach ( $stmts AS $index => $stmt )
		{
			$this->_msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

			if( $this->_schema->tableExists( 'mshop_order' ) === true
				&& $this->_schema->indexExists( 'mshop_order', $index ) === true )
			{
				$this->_execute( $stmt );
				$this->_status( 'dropped' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}

}