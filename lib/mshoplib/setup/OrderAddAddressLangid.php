<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds langid column in order base address table.
 */
class OrderAddAddressLangid extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_order_base_address.langid' => array(
			'ALTER TABLE "mshop_order_base_address" ADD "langid" CHAR(2) NOT NULL AFTER "countryid"',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables' );
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
		$this->msg( 'Adding langid and prodid columns to order tables', 0 ); $this->status( '' );

		$this->process( 'mshop_order_base_address', 'langid', $this->mysql['mshop_order_base_address.langid'] );
	}

	/**
	 * Add columns to tables if they doesn't exist.
	 *
	 * @param string $table Table name
	 * @param string $column Column name to add
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( $table, $column, $stmts )
	{
		$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

		if( $this->schema->tableExists( $table ) === true
			&& $this->schema->columnExists( $table, $column ) === false )
		{
			$this->executeList( $stmts );
			$this->status( 'added' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}