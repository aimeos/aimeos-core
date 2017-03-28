<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Moves catalog tree table to catalog.
 */
class CatalogTreeToCatalog extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_catalog_tree' => array(
			'ALTER TABLE "mshop_catalog_list" DROP FOREIGN KEY "fk_mscatli_parentid"',
			'ALTER TABLE "mshop_catalog_tree" DROP FOREIGN KEY "fk_mscattr_siteid"',
			'ALTER TABLE "mshop_catalog_tree" DROP INDEX "fk_mscattr_siteid"',
			'ALTER TABLE "mshop_catalog_tree" DROP INDEX "idx_mscattr_nleft_nright"',
			'ALTER TABLE "mshop_catalog_tree" CHANGE "id" "id" INTEGER NOT NULL',
			'ALTER TABLE "mshop_catalog_tree" DROP PRIMARY KEY',
			'RENAME TABLE "mshop_catalog_tree" TO "mshop_catalog"',
			'ALTER TABLE "mshop_catalog" ADD CONSTRAINT "pk_mscat_id" PRIMARY KEY ("id")',
			'ALTER TABLE "mshop_catalog" CHANGE "id" "id" INTEGER NOT NULL AUTO_INCREMENT',
			'ALTER TABLE "mshop_catalog_list" ADD CONSTRAINT "fk_mscatli_parentid" FOREIGN KEY ( "parentid" )
				REFERENCES "mshop_catalog" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
			'ALTER TABLE "mshop_catalog" ADD CONSTRAINT "fk_mscat_siteid" FOREIGN KEY ("siteid")
				REFERENCES "mshop_global_site" ("id") ON UPDATE CASCADE ON DELETE CASCADE',
			'ALTER TABLE "mshop_catalog" ADD INDEX "idx_mscat_nleft_nright" ( "nleft", "nright" )',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return [];
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
	 * Renames catalog_tree table if it exists.
	 *
	 * @param array $stmts Associative array of table name and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Renaming catalog tree table', 0 ); $this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true )
			{
				$this->executeList( $stmtList );
				$this->status( 'renamed' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
