<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Migrates type values in order table.
 */
class MW_Setup_Task_OrderMigrateType extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'UPDATE "mshop_order" SET "type" = \'repeat\' WHERE "type" = \'0\'',
		'UPDATE "mshop_order" SET "type" = \'web\' WHERE "type"=\'1\'',
		'UPDATE "mshop_order" SET "type" = \'phone\' WHERE "type"=\'2\''
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
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Migrating order type', 0 ); $this->_status( '' );
		$cntRows = 0;
		$table = 'mshop_order';

		$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

		if( $this->_schema->tableExists( $table ) === true )
		{
			foreach ($stmts AS $sql) {
				$stmt = $this->_conn->create( $sql );
				$result = $stmt->execute();
				$cntRows += $result->affectedRows();
				$result->finish();
			}
			if ($cntRows) {
				$this->_status( sprintf('migrated (%1$d)', $cntRows));
			} else {
				$this->_status('OK');
			}
		}
		else
		{
			$this->_status( 'OK' );
		}

	}

}
