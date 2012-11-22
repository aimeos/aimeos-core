<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: CatalogAddCode.php 14251 2011-12-09 13:36:27Z nsendetzky $
 */


/**
 * Adds code column to catalog table.
 */
class MW_Setup_Task_CatalogAddParentId extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'ALTER TABLE "mshop_catalog" ADD "parentid" INTEGER NOT NULL DEFAULT 0 AFTER "id"',
		'UPDATE "mshop_catalog" SET "parentid" = "id" WHERE "parentid" LIKE 0',
		'ALTER TABLE "mshop_catalog" ADD UNIQUE "unq_mscat_id_pid" ("id", "parentid" )',
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array('CatalogTreeToCatalog');
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
	 * Add column to table if it doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( $stmts )
	{
		$this->_msg( 'Adding parentid column to mshop_catalog', 0 );

		if( $this->_schema->tableExists( 'mshop_catalog' ) === true
			&& $this->_schema->columnExists( 'mshop_catalog', 'parentid' ) === false )
		{
			$this->_executeList( $stmts );
			$this->_status( 'added' );
		}
		else
		{
			$this->_status( 'OK' );
		}
	}
}