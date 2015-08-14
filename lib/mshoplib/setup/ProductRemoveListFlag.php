<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Remove column listflag from product table.
 */
class MW_Setup_Task_ProductRemoveListFlag extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'column' => array(
			'listflag' => 'ALTER TABLE "mshop_product" DROP "listflag"',
		),
		'index' => array(
			'idx_mspro_sid_stat_lf_st_e' => 'ALTER TABLE "mshop_product" DROP INDEX "idx_mspro_sid_stat_lf_st_e"',
			'idx_mspro_id_sid_stat_lf_st_e' => 'ALTER TABLE "mshop_product" DROP INDEX "idx_mspro_id_sid_stat_lf_st_e"',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductInlistRename' );
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
	 * Migrates service text data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Remove listflag from mshop_product table', 0 ); $this->_status( '' );

		$table = 'mshop_product';

		if( $this->_schema->tableExists( $table ) === true )
		{
			foreach( $stmts['column'] as $column => $stmt )
			{
				$this->_msg( sprintf( 'Checking column "%1$s": ', $column ), 1 );

				if( $this->_schema->columnExists( $table, $column ) === true )
				{
					$this->_execute( $stmt );
					$this->_status( 'removed' );
				}
				else {
					$this->_status( 'OK' );
				}
			}

			foreach( $stmts['index'] as $index => $stmt )
			{
				$this->_msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

				if( $this->_schema->indexExists( $table, $index ) === true )
				{
					$this->_execute( $stmt );
					$this->_status( 'removed' );
				} else {
					$this->_status( 'OK' );
				}
			}
		}
	}
}
