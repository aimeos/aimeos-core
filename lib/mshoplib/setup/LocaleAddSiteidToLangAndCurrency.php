<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds siteid column to locale language and currency tables.
 */
class LocaleAddSiteidToLangAndCurrency extends \Aimeos\MW\Setup\Task\Base
{

	private $mysql = array(
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
	protected function mysql()
	{
		$this->process( $this->mysql );
	}

	/**
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Adding siteid to locale tables', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->msg( sprintf( 'Changing table "%1$s": ', $table ), 1 );
			if( $this->schema->tableExists( $table ) && !$this->schema->columnExists( $table, 'siteid' ) )
			{
				$this->executeList( $stmtList );
				$this->status( 'added' );
			} else {
				$this->status( 'OK' );
			}
		}
	}

}
