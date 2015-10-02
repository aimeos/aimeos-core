<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Removes currencyid column from locale site table.
 */
class MW_Setup_Task_LocaleRemoveSiteCurrencyid extends MW_Setup_Task_Abstract
{
	private $mysql = array(
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
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Renames catalog_tree table if it exists.
	 *
	 * @param array $stmts Associative array of table name and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Removing locale site "currencyid" column', 0 ); $this->status( '' );
		$this->msg( sprintf( 'Checking table "%1$s": ', 'mshop_locale_site' ), 1 );

		if( $this->schema->tableExists( 'mshop_locale_site' ) === true
			&& $this->schema->columnExists( 'mshop_locale_site', 'currencyid' ) === true )
		{
			$this->executeList( $stmts );
			$this->status( 'removed' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
