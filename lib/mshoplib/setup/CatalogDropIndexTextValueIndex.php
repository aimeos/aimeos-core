<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


/**
 * Drops the text index without domain in the catalog index text table
 */
class MW_Setup_Task_CatalogDropIndexTextValueIndex extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'idx_msindte_p_s_lt_la_ty_va' => 'ALTER TABLE "mshop_catalog_index_text" DROP INDEX "idx_mscatinte_p_s_lt_la_ty_va"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogAddIndexTextDomain' );
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
	 * Drops the text index without domain in the catalog index text table
	 *
	 * @param array $stmts List of SQL statements to execute
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Drop text index without domain in mshop_catalog_index_text table', 0 );
		$this->_status( '' );

		foreach( $stmts as $index => $stmt )
		{
			$this->_msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

			if( $this->_schema->tableExists( 'mshop_catalog_index_text' ) === true
				&& $this->_schema->indexExists( 'mshop_catalog_index_text', $index ) === true )
			{
				$this->_execute( $stmt );
				$this->_status( 'done' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}

}