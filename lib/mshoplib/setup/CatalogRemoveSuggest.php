<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Removes catalog suggest tables.
 */
class CatalogRemoveSuggest extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
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
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop', 'MShopAddTypeData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
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
		$this->msg( 'Migrating catalog suggest tables', 0 ); $this->status( '' );
		$this->msg( sprintf( 'Checking table "%1$s": ', 'mshop_catalog_suggest' ), 1 );

		if( $this->schema->tableExists( 'mshop_catalog_suggest' ) === true )
		{
			$result = $this->conn->create( $stmts['gettypeid'] )->execute();

			if( ( $row = $result->fetch( \Aimeos\MW\DB\Result\Base::FETCH_NUM ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( 'Product list type "suggestion" is missing' );
			}

			$result->finish();


			$result = $this->conn->create( str_replace( ':typeid', $row[0], $stmts['migrate'] ) )->execute();
			$count = $result->affectedRows();
			$result->finish();


			$this->executeList( $stmts['drop'] );

			$this->status( sprintf( 'migrated (%1$d)', $count ) );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
