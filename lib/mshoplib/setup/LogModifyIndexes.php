<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Modifies indexes in madmin_log.
 */
class LogModifyIndexes extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
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
	protected function mysql()
	{
		$this->process( $this->mysql );
	}
	
	
	/**
	 * Adds and modifies indexes in madmin_log table.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( sprintf( 'Modifying indexes in madmin_log table' ), 0 );
		$this->status( '' );
		
		foreach( $stmts['delete'] as $table => $indexes )
		{
			foreach( $indexes as $index => $stmt )
			{
				$this->msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );
				
				if( $this->schema->tableExists( $table ) === true
					&& $this->schema->indexExists( $table, $index ) === true )
				{
					$this->execute( $stmt );
					$this->status( 'dropped' );
				}
				else
				{
					$this->status( 'OK' );
				}
			}
		}
		
	}
	
}