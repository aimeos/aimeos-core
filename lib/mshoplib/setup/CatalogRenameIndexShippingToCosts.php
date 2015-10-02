<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds priceid/textid column to catalog index price/text table.
 */
class MW_Setup_Task_CatalogRenameIndexShippingToCosts extends MW_Setup_Task_Abstract
{
	private $mysql = 'ALTER TABLE "mshop_catalog_index_price" CHANGE "shipping" "costs" DECIMAL(12,2) NOT NULL';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogAddIndexUniqueIndexes' );
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
	 * @param string $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( $stmts )
	{
		$this->msg( 'Renameing shipping to costs in catalog index price table', 0 );

		if( $this->schema->tableExists( 'mshop_catalog_index_price' ) === true
			&& $this->schema->columnExists( 'mshop_catalog_index_price', 'shipping' ) === true )
		{
			$this->execute( $stmts );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}