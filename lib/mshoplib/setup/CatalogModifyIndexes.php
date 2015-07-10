<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Modifies indexes in the catalog tables.
 */
class MW_Setup_Task_CatalogModifyIndexes extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'add' => array(
			'mshop_catalog_list' => array(
				'fk_mscatli_parentid' => 'ALTER TABLE "mshop_catalog_list" ADD INDEX "fk_mscatli_parentid" ("parentid")'
			),
			'mshop_catalog_site' => array(
				'unq_mscatsi_sid_pid' => 'ALTER TABLE "mshop_catalog_site" ADD UNIQUE INDEX "unq_mscatsi_sid_pid" ("siteid", "parentid")',
				'fk_mscatsi_parentid' => 'ALTER TABLE "mshop_catalog_site" ADD INDEX "fk_mscatsi_parentid" ("parentid")'
			)
		),
		'delete' => array(
			'mshop_catalog' => array(
				'idx_mscat_nleft_nright' => 'ALTER TABLE "mshop_catalog" DROP INDEX "idx_mscat_nleft_nright"'
			),
			'mshop_catalog_list' => array(
				'unq_mscatli_pid_sid_tid_rid_dm' => 'ALTER TABLE "mshop_catalog_list" DROP INDEX "unq_mscatli_pid_sid_tid_rid_dm"'
			),
			'mshop_catalog_site' => array(
				'unq_mscatsi_pid_sid' => 'ALTER TABLE "mshop_catalog_site" DROP INDEX "unq_mscatsi_pid_sid"'
			)
		)
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
	 * Adds and modifies indexes in the mshop_catalog table.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( sprintf( 'Modifying indexes in mshop_catalog tables' ), 0 );
		$this->_status('');

		foreach( $stmts['add'] AS $table => $indexes )
		{
			foreach ( $indexes AS $index => $stmt )
			{
				$this->_msg(sprintf('Checking index "%1$s": ', $index), 1);

				if( $this->_schema->tableExists( $table ) === true
					&& $this->_schema->indexExists( $table, $index ) !== true )
				{
					$this->_execute( $stmt );
					$this->_status( 'added' );
				}
				else
				{
					$this->_status( 'OK' );
				}
			}
		}

		foreach( $stmts['delete'] AS $table => $indexes )
		{
			foreach ( $indexes AS $index => $stmt )
			{
				$this->_msg(sprintf('Checking index "%1$s": ', $index), 1);

				if( $this->_schema->tableExists( $table ) === true
					&& $this->_schema->indexExists( $table, $index ) === true )
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

}