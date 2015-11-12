<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Renames the "refid" column to "parentid"
 */
class CustomerChangeAddressRefidParentid extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'refid' => array(
			'ALTER TABLE "mshop_customer_address" DROP FOREIGN KEY "fk_mscusad_refid"',
			'ALTER TABLE "mshop_customer_address" CHANGE "refid" "parentid" INTEGER NOT NULL',
			'ALTER TABLE "mshop_customer_address" DROP INDEX "idx_mscusad_sid_rid", ADD INDEX "idx_mscusad_sid_pid" ("siteid", "parentid")',
			'ALTER TABLE "mshop_customer_address" ADD CONSTRAINT "fk_mscusad_parentid" FOREIGN KEY ("parentid") REFERENCES "mshop_customer" ("id") ON UPDATE CASCADE ON DELETE CASCADE',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CustomerRenameConstraints' );
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
	 * Changes the column in table
	 *
	 * array string $stmts List of SQL statements for changing the columns
	 */
	protected function process( array $stmts )
	{
		$table = 'mshop_customer_address';
		$this->msg( sprintf( 'Rename "refid" to "parentid" in table "%1$s"', $table ), 0 ); $this->status( '' );

		foreach( $stmts as $column => $stmts )
		{
			$this->msg( sprintf( 'Checking column "%1$s"', $column ), 1 );

			if( $this->schema->tableExists( $table )
				&& $this->schema->columnExists( $table, $column ) === true
			) {
				$this->executeList( $stmts );
				$this->status( 'done' );
			} else {
				$this->status( 'OK' );
			}
		}
	}
}
