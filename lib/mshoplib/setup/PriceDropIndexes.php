<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Removes unused indexes in price table.
 */
class MW_Setup_Task_PriceDropIndexes extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
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
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Removes unused indexes from mshop price table.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Remove unused indexes in mshop_price table', 0 );
		$this->_status( '' );

		foreach ( $stmts AS $index => $stmt )
		{
			$this->_msg( sprintf( 'Checking index "%1$s": ', $index ), 1 );

			if( $this->_schema->tableExists( 'mshop_price' ) === true
				&& $this->_schema->indexExists( 'mshop_price', $index ) === true )
			{
				$this->_execute( $stmt );
				$this->_status( 'dropped' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}

	}

}