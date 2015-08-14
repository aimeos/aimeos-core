<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Modifies indexes in the attribute tables.
 */
class MW_Setup_Task_AttributeModifyIndexes extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'add' => array(
			'mshop_attribute_list' => array(
				'fk_msattli_pid' => 'ALTER TABLE "mshop_attribute_list" ADD INDEX "fk_msattli_pid" ("parentid")',
			)
		),
		'delete' => array(
			'mshop_attribute_list' => array(
				'unq_msattli_pid_sid_tid_rid_dm' => 'ALTER TABLE "mshop_attribute_list" DROP INDEX "unq_msattli_pid_sid_tid_rid_dm"',
				'fk_msattli_parentid' => 'ALTER TABLE "mshop_attribute_list" DROP INDEX "fk_msattli_parentid"',
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
	 * Adds and modifies indexes in the mshop_attribute table.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( sprintf( 'Modifying indexes in mshop_attribute tables' ), 0 );
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