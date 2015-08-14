<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Changes madmin log table to allow site id to be null.
 */
class MW_Setup_Task_LogChangeSiteidNull extends MW_Setup_Task_Abstract
{
	private $_mysql = 'ALTER TABLE "madmin_log" CHANGE COLUMN "siteid" "siteid" INTEGER';

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
	 * Changes site ID to NULL in madmin_log.
	 *
	 * @param string $stmt SQL statement to execute
	 */
	protected function _process( $stmt )
	{
		$table = 'madmin_log';

		$this->_msg( 'Changing site ID to NULL in madmin_log', 0 );
		$this->_status( '' );

		$this->_msg( sprintf( 'Changing table "%1$s": ', $table ), 1 );

		if( $this->_schema->tableExists( $table ) &&
			!$this->_schema->getColumnDetails( $table, 'siteid' )->isNullable() )
		{
			$this->_execute( $stmt );
			$this->_status( 'done' );
		} else {
			$this->_status( 'OK' );
		}

	}

}
