<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes countryid column in customer table.
 */
class CustomerChangeCountryidNull extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = 'ALTER TABLE "mshop_customer" MODIFY "countryid" CHAR(2) NULL';


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
		$column = 'countryid';
		$table = 'mshop_customer';

		$this->msg( sprintf( 'Allow NULL for "%2$s" in table "%1$s"', $table, $column ), 0 );

		if( $this->schema->tableExists( $table )
			&& $this->schema->columnExists( $table, $column ) === true
			&& $this->schema->getColumnDetails( $table, $column )->isNullable() === false
		) {
			$this->execute( $stmt );
			$this->status( 'changed' );
		} else {
			$this->status( 'OK' );
		}
	}
}
