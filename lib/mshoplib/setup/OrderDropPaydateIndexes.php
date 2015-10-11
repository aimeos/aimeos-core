<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Drops the old *_pdate_* indexes in the order tables.
 */
class OrderDropPaydateIndexes extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'idx_msord_sid_pdate_pstat_dstat' => 'ALTER TABLE "mshop_order" DROP INDEX "idx_msord_sid_pdate_pstat_dstat"',
		'idx_msord_sid_mtime' => 'ALTER TABLE "mshop_order" DROP INDEX "idx_msord_sid_mtime"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
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
	 * Adds and modifies indexes in the mshop_catalog table.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Drop old pay date indexes in mshop_order table', 0 );
		$this->status( '' );

		foreach( $stmts as $index => $stmt )
		{
			$this->msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

			if( $this->schema->tableExists( 'mshop_order' ) === true
				&& $this->schema->indexExists( 'mshop_order', $index ) === true )
			{
				$this->execute( $stmt );
				$this->status( 'dropped' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}

}