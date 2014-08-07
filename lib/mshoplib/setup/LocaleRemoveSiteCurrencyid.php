<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Removes currencyid column from locale site table.
 */
class MW_Setup_Task_LocaleRemoveSiteCurrencyid extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'ALTER TABLE "mshop_locale_site" DROP "currencyid"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'GlobalMoveTablesToLocale' );
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
	 * Renames catalog_tree table if it exists.
	 *
	 * @param array $stmts Associative array of table name and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Removing locale site "currencyid" column', 0 ); $this->_status( '' );
		$this->_msg( sprintf( 'Checking table "%1$s": ', 'mshop_locale_site' ), 1 );

		if( $this->_schema->tableExists( 'mshop_locale_site' ) === true
			&& $this->_schema->columnExists( 'mshop_locale_site', 'currencyid' ) === true )
		{
			$this->_executeList( $stmts );
			$this->_status( 'removed' );
		}
		else
		{
			$this->_status( 'OK' );
		}
	}
}
