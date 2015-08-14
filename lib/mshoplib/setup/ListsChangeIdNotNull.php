<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Changes list ids to NOT NULL.
 */
class MW_Setup_Task_ListsChangeIdNotNull extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_text_list_type' => 'ALTER TABLE "mshop_text_list_type" CHANGE "typeid" "typeid" INTEGER NOT NULL',
		'mshop_customer_list_type' => 'ALTER TABLE "mshop_customer_list_type" CHANGE "typeid" "typeid" INTEGER NOT NULL',
		'mshop_media_list_type' => 'ALTER TABLE "mshop_media_list_type" CHANGE "typeid" "typeid" INTEGER NOT NULL',
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
	 * Changes list ids to NOT NULL.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Changing list ids to NOT NULL', 0 );
		$this->_status( '' );
		
		foreach( $stmts as $tablename => $stmt )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $tablename ), 1 );
			
			if( $this->_schema->tableExists( $tablename ) === true
				&& $this->_schema->getColumnDetails( $tablename, 'id' )->isNullable() === true )
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
