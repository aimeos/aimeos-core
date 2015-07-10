<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Drops the old indexes in the catalog tables.
 */
class MW_Setup_Task_CatalogDropIndexCatalogIndexes extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
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
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Adds and modifies indexes in the mshop_catalog table.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Drop old indexes in mshop_catalog_index_catalog table', 0 );
		$this->_status( '' );

		foreach ( $stmts AS $index => $stmt )
		{
			$this->_msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

			if( $this->_schema->tableExists( 'mshop_catalog_index_catalog' ) === true
				&& $this->_schema->indexExists( 'mshop_catalog_index_catalog', $index ) === true )
			{
				$this->_execute( $stmt );
				$this->_status( 'dropped' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}

}