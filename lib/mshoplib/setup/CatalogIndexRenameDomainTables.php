<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


class CatalogIndexRenameDomainTables extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_catalog_index_attribute' => 'RENAME TABLE "mshop_catalog_index_attribute" TO "mshop_index_attribute"',
		'mshop_catalog_index_catalog' => 'RENAME TABLE "mshop_catalog_index_catalog" TO "mshop_index_catalog"',
		'mshop_catalog_index_price' => 'RENAME TABLE "mshop_catalog_index_price" TO "mshop_index_price"',
		'mshop_catalog_index_text' => 'RENAME TABLE "mshop_catalog_index_text" TO "mshop_index_text"',
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array(
			'CatalogAddIndexPriceidTextid', 'CatalogDropIndexLocaleConstraints', 'CatalogDropIndexCatalogIndexes',
			'CatalogChangeIndexAttridPriceidTextidNull', 'CatalogAddIndexUniqueIndexes', 'CatalogAddIndexTypeCode',
			'CatalogAddIndexTextDomain', 'CatalogAddIndexPriceidTextid', 'TablesAddLogColumns', 'TablesChangeSiteidNotNull'
		);
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
	 * Rename catalog index tables to index if they exist
	 *
	 * @param array $stmts List of SQL statements to execute for renaming
	 */
	protected function process( $stmts )
	{
		$this->msg( 'Renaming catalog index tables to index', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->msg( sprintf( 'Checking table "%1$s"', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true )
			{
				$this->execute( $stmt );
				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
