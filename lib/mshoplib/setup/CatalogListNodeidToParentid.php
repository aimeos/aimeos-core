<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Renames nodeid column to parentid in catalog list table.
 */
class CatalogListNodeidToParentid extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_catalog_list' => array(
			'ALTER TABLE "mshop_catalog_list" DROP FOREIGN KEY "fk_mscatli_nodeid"',
			'ALTER TABLE "mshop_catalog_list" CHANGE "nodeid" "parentid" INT( 11 ) NOT NULL',
			'ALTER TABLE "mshop_catalog_list" DROP INDEX "unq_mscatli_nid_sid_dom"',
			'ALTER TABLE "mshop_catalog_list" ADD UNIQUE "unq_mscatli_pid_sid_dom_rid" ( "parentid" , "siteid" , "domain" , "refid" )',
			'ALTER TABLE "mshop_catalog_list" DROP INDEX "fk_mscatli_nodeid"',
			'ALTER TABLE "mshop_catalog_list" ADD CONSTRAINT "fk_mscatli_parentid" FOREIGN KEY ( "parentid" ) REFERENCES "mshop_catalog_tree" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
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
	 * Renames the nodeid column.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Renaming column nodeid of catalog list table', 0 ); $this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true
				&& $this->schema->columnExists( $table, 'nodeid' ) === true )
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
