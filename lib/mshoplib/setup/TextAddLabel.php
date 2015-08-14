<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds label column to text table.
 */
class MW_Setup_Task_TextAddLabel extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'ALTER TABLE "mshop_text" ADD "label" VARCHAR(255) NOT NULL AFTER "domain"',
		'UPDATE "mshop_text" SET "label" = "content" WHERE "label" = \'\'',
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TextChangeTextToContent' );
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
	 * Migrates service text data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Adding label to mshop text table', 0 );

		if( $this->_schema->tableExists( 'mshop_text' ) === true
			&& $this->_schema->columnExists( 'mshop_text', 'label' ) === false )
		{
			$this->_executeList( $stmts );
			$this->_status( 'added' );
		} else {
			$this->_status( 'OK' );
		}
	}

}
