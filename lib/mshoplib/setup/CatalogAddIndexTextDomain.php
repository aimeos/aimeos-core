<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds domain column to catalog index text table.
 */
class MW_Setup_Task_CatalogAddIndexTextDomain extends MW_Setup_Task_Base
{
	private $mysql = array(
		'ALTER TABLE "mshop_catalog_index_text" ADD "domain" VARCHAR(32) NOT NULL AFTER "type"',
		'UPDATE "mshop_catalog_index_text" SET "domain" = \'product\'',
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
		$this->msg( 'Adding domain column to catalog index text table', 0 );
		$this->status( '' );

		$table = 'mshop_catalog_index_text';
		$column = 'domain';

		$this->msg( sprintf( 'Checking table "%1$s" for column "%2$s"', $table, $column ), 1 );

		if( $this->schema->tableExists( $table ) === true
			&& $this->schema->columnExists( $table, $column ) === false )
		{
			$this->executeList( $stmts );
			$this->status( 'added' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
