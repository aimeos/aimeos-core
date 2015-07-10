<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Changes countryid/langid columns in supplier address table.
 */
class MW_Setup_Task_SupplierChangeAddressLangidCountryidNull extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'langid' => 'ALTER TABLE "mshop_supplier_address" MODIFY "langid" VARCHAR(5) NULL',
		'countryid' => 'ALTER TABLE "mshop_supplier_address" MODIFY "countryid" CHAR(2) NULL',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
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
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Changes columns in table.
	 *
	 * array string $stmts List of SQL statements to execute for changing columns
	 */
	protected function _process( array $stmts )
	{
		$table = 'mshop_supplier_address';
		$this->_msg( sprintf( 'Allow NULL in table "%1$s"', $table ), 0 ); $this->_status( '' );

		foreach( $stmts as $column => $stmt )
		{
			$this->_msg( sprintf( 'Checking column "%1$s"', $column ), 1 );

			if( $this->_schema->tableExists( $table )
				&& $this->_schema->columnExists( $table, $column ) === true
				&& $this->_schema->getColumnDetails( $table, $column )->isNullable() === false
			) {
				$this->_execute( $stmt );
				$this->_status( 'changed' );
			} else {
				$this->_status( 'OK' );
			}
		}
	}
}
