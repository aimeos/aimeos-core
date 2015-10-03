<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Changes list ids to NOT NULL.
 */
class MW_Setup_Task_ListsChangeIdNotNull extends MW_Setup_Task_Base
{
	private $mysql = array(
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
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Changes list ids to NOT NULL.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Changing list ids to NOT NULL', 0 );
		$this->status( '' );
		
		foreach( $stmts as $tablename => $stmt )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $tablename ), 1 );
			
			if( $this->schema->tableExists( $tablename ) === true
				&& $this->schema->getColumnDetails( $tablename, 'id' )->isNullable() === true )
			{
				$this->execute( $stmt );
				$this->status( 'migrated' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
