<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Renames column "text" to "content".
 */
class MW_Setup_Task_TextChangeTextToContent extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'ALTER TABLE "mshop_text" CHANGE "text" "content" TEXT NOT NULL DEFAULT \'\'',
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
	 * Migrates service text data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Changing mshop_text.text to mshop_text.content', 0 );

		if( $this->_schema->tableExists( 'mshop_text' ) === true
			&& $this->_schema->columnExists( 'mshop_text', 'content' ) === false )
		{
			$this->_executeList( $stmts );
			$this->_status( 'migrated' );
		}
		else
		{
			$this->_status( 'OK' );
		}
	}

}
