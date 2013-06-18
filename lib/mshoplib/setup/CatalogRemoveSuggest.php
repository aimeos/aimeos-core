<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Removes catalog suggest tables.
 */
class MW_Setup_Task_CatalogRemoveSuggest extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'gettypeid' => 'SELECT "id" FROM "mshop_product_list_type" WHERE "code" = \'suggestion\'',
		'migrate' => '
			INSERT INTO "mshop_product_list" ("parentid", "siteid", "typeid", "domain", "refid", "pos")
			SELECT "prodid", "siteid", :typeid, \'product\', "refid", "pos"
			FROM "mshop_catalog_suggest" mcs
			JOIN "mshop_catalog_suggest_product" mcsp ON mcs."id" = mcsp."catsugid"
		',
		'drop' => array(
			'DROP TABLE "mshop_catalog_suggest_product"',
			'DROP TABLE "mshop_catalog_suggest"',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array('TablesCreateMShop', 'MShopAddTypeData');
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array();
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
		$this->_msg( 'Migrating catalog suggest tables', 0 ); $this->_status( '' );
		$this->_msg( sprintf( 'Checking table "%1$s": ', 'mshop_catalog_suggest' ), 1 );

		if( $this->_schema->tableExists( 'mshop_catalog_suggest' ) === true )
		{
			$result = $this->_conn->create( $stmts['gettypeid'] )->execute();

			if( ( $row = $result->fetch( MW_DB_Result_Abstract::FETCH_NUM ) ) === false ) {
				throw new MW_Setup_Exception( 'Product list type "suggestion" is missing' );
			}

			$result->finish();


			$result = $this->_conn->create( str_replace( ':typeid', $row[0], $stmts['migrate'] ) )->execute();
			$count = $result->affectedRows();
			$result->finish();


			$this->_executeList( $stmts['drop'] );

			$this->_status( sprintf( 'migrated (%1$d)', $count ) );
		}
		else
		{
			$this->_status( 'OK' );
		}
	}
}
