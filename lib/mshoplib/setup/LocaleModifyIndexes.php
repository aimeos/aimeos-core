<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Modifies indexes in the locale tables.
 */
class MW_Setup_Task_LocaleModifyIndexes extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'add' => array (
			'mshop_locale_language' => array (
				'fk_mslocla_siteid' => 'ALTER TABLE "mshop_locale_language" ADD INDEX "fk_mslocla_siteid" ("siteid")'
			)
		),
		'delete' => array (
			'mshop_locale_language' => array (
				'idx_mslocla_sid_status' => 'ALTER TABLE "mshop_locale_language" DROP INDEX "idx_mslocla_sid_status"'
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
	 * @return array List of task names
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
	 * Adds and modifies indexes in the locale tables.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( sprintf( 'Modifying indexes in mshop_locale tables' ), 0 );
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