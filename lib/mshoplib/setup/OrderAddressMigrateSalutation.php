<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Migrates salutation values in order base address.
 */
class MW_Setup_Task_OrderAddressMigrateSalutation extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'UPDATE "mshop_order_base_address" SET "salutation" = \'unknown\' WHERE "salutation" = \'0\'',
		'UPDATE "mshop_order_base_address" SET "salutation" = \'company\' WHERE "salutation"=\'1\'',
		'UPDATE "mshop_order_base_address" SET "salutation" = \'mrs\' WHERE "salutation"=\'2\'',
		'UPDATE "mshop_order_base_address" SET "salutation" = \'miss\' WHERE "salutation"=\'3\'',
		'UPDATE "mshop_order_base_address" SET "salutation" = \'mr\' WHERE "salutation"=\'4\''
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables' );
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
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Migrating order address salutations', 0 ); $this->_status( '' );
		$cntRows = 0;
		$table = 'mshop_order_base_address';

		$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

		if( $this->_schema->tableExists( $table ) === true )
		{
			foreach( $stmts as $sql ) {
				$stmt = $this->_conn->create( $sql );
				$result = $stmt->execute();
				$cntRows += $result->affectedRows();
				$result->finish();
			}
			if( $cntRows ) {
				$this->_status( sprintf( 'migrated (%1$d)', $cntRows ) );
			} else {
				$this->_status( 'OK' );
			}
		}
		else
		{
			$this->_status( 'OK' );
		}

	}
}
