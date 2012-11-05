<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: CatalogListNodeidToParentid.php 14251 2011-12-09 13:36:27Z nsendetzky $
 */


/**
 * Renames nodeid column to parentid in catalog list table.
 */
class MW_Setup_Task_CatalogListNodeidToParentid extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
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
	 * Renames the nodeid column.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Renaming column nodeid of catalog list table', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true
				&& $this->_schema->columnExists( $table, 'nodeid' ) === true )
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
