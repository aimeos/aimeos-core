<?php

/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds verification date column to customer table.
 */
class CustomerAddVerificationDate extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'ALTER TABLE "mshop_customer" ADD "vdate" DATE NULL AFTER "status"',
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
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$table = 'mshop_customer';
		$this->msg( sprintf( 'Adding verification date column to table "%1$s"', $table ), 0 );

		if( $this->schema->tableExists( $table )
			&& $this->schema->columnExists( $table, 'vdate' ) === false )
		{
			$this->executeList( $stmts );
			$this->status( 'done' );
		} else {
			$this->status( 'OK' );
		}
	}
}