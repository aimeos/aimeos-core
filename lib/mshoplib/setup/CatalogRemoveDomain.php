<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: CatalogRemoveDomain.php 14251 2011-12-09 13:36:27Z nsendetzky $
 */


/**
 * Removes domain column from catalog table.
 */
class MW_Setup_Task_CatalogRemoveDomain extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'ALTER TABLE "mshop_catalog" DROP "domain"',
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
		return array( 'CatalogTreeToCatalog' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Renames catalog_tree table if it exists.
	 *
	 * @param array $stmts Associative array of table name and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Removing catalog "domain" column', 0 ); $this->_status( '' );
		$this->_msg( sprintf( 'Checking table "%1$s": ', 'mshop_catalog' ), 1 );

		if( $this->_schema->tableExists( 'mshop_catalog' ) === true
			&& $this->_schema->columnExists( 'mshop_catalog', 'domain' ) === true )
		{
			$this->_executeList( $stmts );
			$this->_status( 'removed' );
		}
		else
		{
			$this->_status( 'OK' );
		}
	}
}
