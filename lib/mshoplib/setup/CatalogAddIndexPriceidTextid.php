<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds priceid/textid column to index price/text table.
 */
class CatalogAddIndexPriceidTextid extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_catalog_index_price.priceid' => array(
			'TRUNCATE TABLE "mshop_catalog_index_price"',
			'ALTER TABLE "mshop_catalog_index_price" ADD "priceid" INTEGER NULL AFTER "siteid"',
			'ALTER TABLE "mshop_catalog_index_price" ADD FOREIGN KEY ("priceid") REFERENCES "mshop_price" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
		),
		'mshop_catalog_index_text.textid' => array(
			'TRUNCATE TABLE "mshop_catalog_index_text"',
			'ALTER TABLE "mshop_catalog_index_text" ADD "textid" INTEGER NULL AFTER "siteid"',
			'ALTER TABLE "mshop_catalog_index_text" ADD FOREIGN KEY ("textid") REFERENCES "mshop_text" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
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
		$this->msg( 'Adding reference ID columns to index tables', 0 );
		$this->status( '' );

		foreach( $stmts as $id => $sql )
		{
			$parts = explode( '.', $id );
			$this->msg( sprintf( 'Checking table "%1$s" for column "%2$s"', $parts[0], $parts[1] ), 1 );

			if( $this->schema->tableExists( $parts[0] ) === true
				&& $this->schema->columnExists( $parts[0], $parts[1] ) === false )
			{
				$this->executeList( $sql );
				$this->status( 'added' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}