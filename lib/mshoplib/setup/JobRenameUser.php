<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Renames "user" column to "editor" in job table.
 */
class MW_Setup_Task_JobRenameUser extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'user' => 'ALTER TABLE "madmin_job" CHANGE "user" "editor" VARCHAR(255) NOT NULL',
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
		$this->_process($this->_mysql);
	}

	/**
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$table = "madmin_job";
		$this->_msg( sprintf( 'Renaming in "%1$s" column "user" to "editor".', $table ), 0 );
		$this->_status( '' );

		foreach ( $stmts AS $column => $stmt )
		{
			$this->_msg( sprintf( 'Checking column "%1$s": ', $column ), 1 );

			if( $this->_schema->tableExists( $table ) === true &&
				$this->_schema->columnExists( $table, $column ) === true )
			{
				$this->_execute( $stmt );
				$this->_status( 'renamed' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}

}
