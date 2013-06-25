<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Changes type of supplier_address_id from BIGINT to INT.
 */
class MW_Setup_Task_SupplierChangeAddressIdToInteger extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_supplier_address' => '
			ALTER TABLE "mshop_supplier_address" MODIFY "id" INTEGER NOT NULL AUTO_INCREMENT
		',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array('SupplierRenameConstraints');
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array('TablesCreateMShop');
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Change type of supplier_address_id from BIGINT to INT.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Altering type of supplier_address id', 0 );

		foreach( $stmts as $table => $stmt )
		{
			if( $this->_schema->tableExists( $table )
				&& strtolower( $this->_schema->getColumnDetails( $table, 'id')->getDataType() ) == 'bigint' )
			{
				$this->_execute( $stmt );
				$this->_status( 'changed' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}
