<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Modifies indexes in the text tables.
 */
class MW_Setup_Task_TextModifyIndexes extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'add' => array(
			'mshop_text_list' => array(
				'fk_mstexli_pid' => 'ALTER TABLE "mshop_text_list" ADD INDEX "fk_mstexli_pid" ("parentid")',
			)
		),
		'delete' => array(
			'mshop_text_list' => array(
				'fk_mstexli_parentid' => 'ALTER TABLE "mshop_text_list" DROP INDEX "fk_mstexli_parentid"',
				'unq_mstexli_pid_sid_tid_rid_dm' => 'ALTER TABLE "mshop_text_list" DROP INDEX "unq_mstexli_pid_sid_tid_rid_dm"',
			)
		)
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
	 * Adds and modifies indexes in the text tables.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( sprintf( 'Modifying indexes in mshop_text tables' ), 0 );
		$this->_status( '' );

		foreach( $stmts['add'] AS $table => $indexes )
		{
			foreach( $indexes AS $index => $stmt )
			{
				$this->_msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

				if( $this->_schema->tableExists( $table ) === true
					&& $this->_schema->indexExists( $table, $index ) !== true )
				{
					$this->_execute( $stmt );
					$this->_status( 'added' );
				}
				else
				{
					$this->_status( 'OK' );
				}
			}
		}

		foreach( $stmts['delete'] AS $table => $indexes )
		{
			foreach( $indexes AS $index => $stmt )
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