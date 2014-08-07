<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Drops the old idx_mscat_sid_nleft_nright index in the catalog tables.
 */
class MW_Setup_Task_CatalogDropSidNleftNrightIndex extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'idx_mscat_sid_nleft_nright' => 'ALTER TABLE "mshop_catalog" DROP INDEX "idx_mscat_sid_nleft_nright"',
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
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Drops idx_mscat_sid_nleft_nright index in the mshop_catalog table.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Drop old index in mshop_catalog table', 0 );
		$this->_status( '' );

		foreach ( $stmts AS $index => $stmt )
		{
			$this->_msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

			if( $this->_schema->tableExists( 'mshop_catalog' ) === true
				&& $this->_schema->indexExists( 'mshop_catalog', $index ) === true )
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