<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes countryid/langid columns in order address table.
 */
class OrderChangeAddressLangidCountryidNull extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'langid' => 'ALTER TABLE "mshop_order_base_address" MODIFY "langid" VARCHAR(5) NULL',
		'countryid' => 'ALTER TABLE "mshop_order_base_address" MODIFY "countryid" CHAR(2) NULL',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderAddAddressLangid' );
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
	 * Changes columns in table.
	 *
	 * array string $stmts List of SQL statements to execute for changing columns
	 */
	protected function process( array $stmts )
	{
		$table = 'mshop_order_base_address';
		$this->msg( sprintf( 'Allow NULL in table "%1$s"', $table ), 0 ); $this->status( '' );

		foreach( $stmts as $column => $stmt )
		{
			$this->msg( sprintf( 'Checking column "%1$s"', $column ), 1 );

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
}
