<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes the attrid/priceid/textid column to allow NULL in index attr/price/text table.
 */
class CatalogChangeIndexAttridPriceidTextidNull extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_catalog_index_attribute' => array(
			'attrid' => 'ALTER TABLE "mshop_catalog_index_attribute" MODIFY "attrid" INTEGER NULL',
		),
		'mshop_catalog_index_price' => array(
			'priceid' => 'ALTER TABLE "mshop_catalog_index_price" MODIFY "priceid" INTEGER NULL',
		),
		'mshop_catalog_index_text' => array(
			'textid' => 'ALTER TABLE "mshop_catalog_index_text" MODIFY "textid" INTEGER NULL',
		),
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogAddIndexPriceidTextid' );
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
		$this->msg( 'Changing reference ID columns of index tables to NULL', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $pairs )
		{
			foreach( $pairs as $column => $sql )
			{
				$this->msg( sprintf( 'Checking table "%1$s" and column "%2$s"', $table, $column ), 1 );

				if( $this->schema->tableExists( $table ) === true
					&& $this->schema->columnExists( $table, $column ) === true
					&& $this->schema->getColumnDetails( $table, $column )->isNullable() === false )
				{
					$this->execute( $sql );
					$this->status( 'changed' );
				}
				else
				{
					$this->status( 'OK' );
				}
			}
		}
	}
}
