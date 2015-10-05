<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Drops the old indexes in the catalog tables.
 */
class CatalogDropIndexCatalogIndexes extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'idx_mscatinca_s_lt_ca_po' => 'ALTER TABLE "mshop_catalog_index_catalog" DROP INDEX "idx_mscatinca_s_lt_ca_po"',
		'idx_mscatinca_p_s_lt_ca_po' => 'ALTER TABLE "mshop_catalog_index_catalog" DROP INDEX "idx_mscatinca_p_s_lt_ca_po"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
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
	 * Adds and modifies indexes in the mshop_catalog table.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Drop old indexes in mshop_catalog_index_catalog table', 0 );
		$this->status( '' );

		foreach( $stmts as $index => $stmt )
		{
			$this->msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

			if( $this->schema->tableExists( 'mshop_catalog_index_catalog' ) === true
				&& $this->schema->indexExists( 'mshop_catalog_index_catalog', $index ) === true )
			{
				$this->execute( $stmt );
				$this->status( 'dropped' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}

}