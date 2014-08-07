<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Moves catalog tree table to catalog.
 */
class MW_Setup_Task_CatalogTreeToCatalog extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
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
		return array();
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
	 * Renames catalog_tree table if it exists.
	 *
	 * @param array $stmts Associative array of table name and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Renaming catalog tree table', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true )
			{
				$this->_executeList( $stmtList );
				$this->_status( 'renamed' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}
