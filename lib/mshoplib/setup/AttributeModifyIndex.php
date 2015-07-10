<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Renames index in the attribute tables.
 */
class MW_Setup_Task_AttributeModifyIndex extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_attribute' => array (
				'dx_msatt_sid_dom_editor' => 'ALTER TABLE "mshop_attribute" DROP INDEX "dx_msatt_sid_dom_editor"',
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
	 * Modifies indexes in the mshop_attribute table.
	 *
	 * @param array $stmts List of SQL statements to execute for renaming columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( sprintf( 'Modifying index in mshop_attribute table' ), 0 );
		$this->_status('');

		foreach( $stmts AS $table => $indexes )
		{
			foreach ( $indexes AS $index => $stmt )
			{
				$this->_msg(sprintf('Checking index "%1$s": ', $index), 1);

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
