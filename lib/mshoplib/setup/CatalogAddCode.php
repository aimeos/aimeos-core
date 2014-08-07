<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds code column to catalog table.
 */
class MW_Setup_Task_CatalogAddCode extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'ALTER TABLE "mshop_catalog" ADD "code" VARCHAR(32) NOT NULL DEFAULT \'\' AFTER "level"',
		'UPDATE "mshop_catalog" SET "code" = "id" WHERE "code" LIKE \'\'',
		'ALTER TABLE "mshop_catalog" ADD UNIQUE "unq_mscat_sid_code" ("siteid", "code" )',
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array('CatalogTreeToCatalog');
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
	 * Add column to table if it doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( $stmts )
	{
		$this->_msg( 'Adding code column to mshop_catalog', 0 );

		if( $this->_schema->tableExists( 'mshop_catalog' ) === true
			&& $this->_schema->columnExists( 'mshop_catalog', 'code' ) === false )
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