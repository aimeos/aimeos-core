<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds siteid column to locale language and currency tables.
 */
class MW_Setup_Task_LocaleAddSiteidToLangAndCurrency extends MW_Setup_Task_Abstract
{

	private $_mysql = array(
		'mshop_locale_currency' => array(
			'ALTER TABLE "mshop_locale_currency" ADD "siteid" INT NULL AFTER "id"',
		),
		'mshop_locale_language' => array(
			'ALTER TABLE "mshop_locale_language" ADD "siteid" INT NULL AFTER "id"',
		),
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
		$this->_process($this->_mysql);
	}

	/**
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg('Adding siteid to locale tables', 0);
		$this->_status('');

		foreach ( $stmts as $table => $stmtList )
		{
			$this->_msg(sprintf('Changing table "%1$s": ', $table), 1);
			if ( $this->_schema->tableExists($table) && !$this->_schema->columnExists( $table, 'siteid' )  )
			{
				$this->_executeList($stmtList);
				$this->_status('added');
			} else {
				$this->_status('OK');
			}
		}
	}

}
