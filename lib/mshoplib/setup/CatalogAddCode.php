<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds code column to catalog table.
 */
class CatalogAddCode extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
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
		return array( 'CatalogTreeToCatalog' );
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
		$this->msg( 'Adding code column to mshop_catalog', 0 );

		if( $this->schema->tableExists( 'mshop_catalog' ) === true
			&& $this->schema->columnExists( 'mshop_catalog', 'code' ) === false )
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