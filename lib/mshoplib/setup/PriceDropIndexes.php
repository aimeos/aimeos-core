<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Removes unused indexes in price table.
 */
class MW_Setup_Task_PriceDropIndexes extends MW_Setup_Task_Base
{
	private $mysql = array(
		'idx_mspri_sid_currid' => 'ALTER TABLE "mshop_price" DROP INDEX "idx_mspri_sid_currid"',
		'idx_mspri_sid_quantity' => 'ALTER TABLE "mshop_price" DROP INDEX "idx_mspri_sid_quantity"',
		'idx_mspri_sid_value' => 'ALTER TABLE "mshop_price" DROP INDEX "idx_mspri_sid_value"',
		'idx_mspri_sid_shipping' => 'ALTER TABLE "mshop_price" DROP INDEX "idx_mspri_sid_shipping"',
		'idx_mspri_sid_rebate' => 'ALTER TABLE "mshop_price" DROP INDEX "idx_mspri_sid_rebate"',
		'idx_mspri_sid_taxrate' => 'ALTER TABLE "mshop_price" DROP INDEX "idx_mspri_sid_taxrate"',
		'idx_mspri_sid_mtime' => 'ALTER TABLE "mshop_price" DROP INDEX "idx_mspri_sid_mtime"',
		'idx_mspri_sid_ctime' => 'ALTER TABLE "mshop_price" DROP INDEX "idx_mspri_sid_ctime"',
		'idx_mspri_sid_editor' => 'ALTER TABLE "mshop_price" DROP INDEX "idx_mspri_sid_editor"',
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
	 * Removes unused indexes from mshop price table.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Remove unused indexes in mshop_price table', 0 );
		$this->status( '' );

		foreach( $stmts as $index => $stmt )
		{
			$this->msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

			if( $this->schema->tableExists( 'mshop_price' ) === true
				&& $this->schema->indexExists( 'mshop_price', $index ) === true )
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