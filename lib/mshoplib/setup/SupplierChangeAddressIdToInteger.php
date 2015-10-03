<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Changes type of supplier_address_id from BIGINT to INT.
 */
class MW_Setup_Task_SupplierChangeAddressIdToInteger extends MW_Setup_Task_Base
{
	private $mysql = array(
		'mshop_supplier_address' => '
			ALTER TABLE "mshop_supplier_address" MODIFY "id" INTEGER NOT NULL AUTO_INCREMENT
		',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'SupplierRenameConstraints' );
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
	 * Change type of supplier_address_id from BIGINT to INT.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Altering type of supplier_address id', 0 );

		foreach( $stmts as $table => $stmt )
		{
			if( $this->schema->tableExists( $table )
				&& strtolower( $this->schema->getColumnDetails( $table, 'id' )->getDataType() ) == 'bigint' )
			{
				$this->execute( $stmt );
				$this->status( 'changed' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
