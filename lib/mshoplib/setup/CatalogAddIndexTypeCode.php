<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds type/code column to catalog index attribute table.
 */
class MW_Setup_Task_CatalogAddIndexTypeCode extends MW_Setup_Task_Abstract
{
	private $mysql = array(
		'type' => array(
			'ALTER TABLE "mshop_catalog_index_attribute" ADD "type" VARCHAR(32) NOT NULL AFTER "listtype"',
		),
		'code' => array(
			'ALTER TABLE "mshop_catalog_index_attribute" ADD "code" VARCHAR(32) NOT NULL AFTER "type"',
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
	protected function mysql()
	{
		$this->process( $this->mysql );
	}
	
	/**
	 * Add column to table if it doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( $stmts )
	{
		$this->msg( 'Adding reference ID columns to catalog index tables', 0 );
		$this->status( '' );
	
		if( $this->schema->tableExists( 'mshop_catalog_index_attribute' ) === true )
		{
			foreach( $stmts as $id => $sql )
			{
				$this->msg( sprintf( 'Checking table "%1$s" for column "%2$s"', 'mshop_catalog_index_attribute', $id ), 1 );
				
				if( $this->schema->columnExists( 'mshop_catalog_index_attribute', $id ) === false )
				{
					$this->executeList( $sql );
					$this->status( 'added' );
				} 
				else 
				{
					$this->status( 'OK' );
				}
			}
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}