<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: JobTableConvertToInnoDB.php 1316 2012-10-19 19:49:23Z nsendetzky $
 */


/**
 * Converts job table to InnoDB.
 */
class MW_Setup_Task_JobTableConvertToInnoDB extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'convert' => 'ALTER TABLE "madmin_job" ENGINE=InnoDB',
		'check' => 'SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?'
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
	 * @return array List of task names
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
	 * Converts MyISAM table to InnoDB.
	 *
	 * @param array $stmts List of SQL statements to execute for changing table
	 */
	protected function _process( array $stmts )
	{
		$table = "madmin_job";
		$this->_msg( sprintf( 'Converting "%1$s" to InnoDB".', $table ), 0 );

		$stmt = $this->_conn->create( $stmts['check'] );
		$stmt->bind( 1, $this->_schema->getDBName() );
		$stmt->bind( 2, $table );
		$result = $stmt->execute();
		$dbname = $result->fetch();
		$result->finish();

		if( $this->_schema->tableExists( $table ) === true
			&& $dbname['ENGINE'] === 'MyISAM' )
		{
			$this->_execute( $stmts['convert'] );
			$this->_status( 'converted' );
		}
		else
		{
			$this->_status( 'OK' );
		}
	}

}
