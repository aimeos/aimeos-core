<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Renames columns of order table.
 */
class OrderRenameColumns extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'ddate' => 'ALTER TABLE "mshop_order" CHANGE "ddate" "datedelivery" DATETIME DEFAULT NULL',
		'pdate' => 'ALTER TABLE "mshop_order" CHANGE "pdate" "datepayment" DATETIME NOT NULL',
		'pstatus' => 'ALTER TABLE "mshop_order" CHANGE "pstatus" "statuspayment" SMALLINT NOT NULL DEFAULT -1',
		'dstatus' => 'ALTER TABLE "mshop_order" CHANGE "dstatus" "statusdelivery" SMALLINT NOT NULL DEFAULT -1',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameConstraints' );
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
		$this->msg( 'Renaming order columns pdate,ddate,dstatus,pstatus', 0 );
		$this->status( '' );

		foreach( $stmts as $col => $stmt )
		{
			$this->msg( sprintf( 'Checking columne "%1$s": ', $col ), 1 );

			if( $this->schema->columnExists( 'mshop_order', $col ) )
			{
				$this->execute( $stmt );
				$this->status( 'renamed' );
			} else {
				$this->status( 'OK' );
			}
		}
	}

}
