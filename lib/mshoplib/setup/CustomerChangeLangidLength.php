<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes langid column in customer table.
 */
class CustomerChangeLangidLength extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = 'ALTER TABLE "mshop_customer" MODIFY "langid" VARCHAR(5) NULL';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CustomerAddColumns' );
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
	 * Changes column in table.
	 *
	 * @param string $stmt SQL statement to execute for changing column
	 */
	protected function process( $stmt )
	{
		$column = 'langid';
		$table = 'mshop_customer';
		$this->msg( sprintf( 'Changing length of "%2$s" in table "%1$s"', $table, $column ), 0 );

		if( $this->schema->tableExists( $table )
			&& $this->schema->columnExists( $table, $column ) === true
			&& $this->schema->getColumnDetails( $table, $column )->getMaxLength() === 2
		) {
			$this->execute( $stmt );
			$this->status( 'changed' );
		} else {
			$this->status( 'OK' );
		}
	}
}