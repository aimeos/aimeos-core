<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Drops the old idx_mscat_sid_nleft_nright index in the catalog tables.
 */
class MW_Setup_Task_CatalogDropSidNleftNrightIndex extends MW_Setup_Task_Abstract
{
	private $mysql = array(
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
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Drops idx_mscat_sid_nleft_nright index in the mshop_catalog table.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Drop old index in mshop_catalog table', 0 );
		$this->status( '' );

		foreach( $stmts as $index => $stmt )
		{
			$this->msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

			if( $this->schema->tableExists( 'mshop_catalog' ) === true
				&& $this->schema->indexExists( 'mshop_catalog', $index ) === true )
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