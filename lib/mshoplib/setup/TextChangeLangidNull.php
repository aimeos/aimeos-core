<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: TextChangeLangidNull.php 14251 2011-12-09 13:36:27Z nsendetzky $
 */


/**
 * Changes langid column to allow NULL values.
 */
class MW_Setup_Task_TextChangeLangidNull extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'langid' => 'ALTER TABLE "mshop_text" CHANGE "langid" "langid" CHAR(2) DEFAULT NULL',
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
		$this->_msg( 'Changing langid of mshop_text table', 0 ); $this->_status( '' );

		foreach( $stmts as $column => $stmt )
		{
			$this->_msg( sprintf( 'Checking column "%1$s": ', $column ), 1 );

			if( $this->_schema->tableExists( 'mshop_text' ) === true
				&& $this->_schema->columnExists( 'mshop_text', $column ) === true
				&& $this->_schema->getColumnDetails( 'mshop_text', $column )->isNullable() === false )
			{
				$this->_execute( $stmt );
				$this->_status( 'migrated' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}
