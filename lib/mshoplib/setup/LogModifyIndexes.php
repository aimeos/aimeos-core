<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Modifies indexes in madmin_log.
 */
class MW_Setup_Task_LogModifyIndexes extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'delete' => array(
			'madmin_log' => array(
				'idx_malog_facility_time_prio' => 'ALTER TABLE "madmin_log" DROP INDEX "idx_malog_facility_time_prio"',
				'idx_malog_timestamp' => 'ALTER TABLE "madmin_log" DROP INDEX "idx_malog_timestamp"'
			)
		),
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
		return array( 'TablesCreateMAdmin' );
	}
	
	
	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}
	
	
	/**
	 * Adds and modifies indexes in madmin_log table.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( sprintf( 'Modifying indexes in madmin_log table' ), 0 );
		$this->_status( '' );
		
		foreach( $stmts['delete'] as $table => $indexes )
		{
			foreach( $indexes as $index => $stmt )
			{
				$this->_msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );
				
				if( $this->_schema->tableExists( $table ) === true
					&& $this->_schema->indexExists( $table, $index ) === true )
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
	
}