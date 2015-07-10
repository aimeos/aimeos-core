<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds order ID column to order base service tables.
 */
class MW_Setup_Task_OrderAddBaseServiceServiceid extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'ALTER TABLE "mshop_order_base_service" ADD "servid" VARCHAR(32) NOT NULL COLLATE utf8_bin AFTER "siteid"',
		'UPDATE "mshop_order_base_service" o SET "servid" = ( SELECT s."id" FROM "mshop_service" s WHERE s."siteid" = o."siteid" AND s."code" = o."code" LIMIT 1 ) WHERE "servid" = \'\'',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array('OrderRenameTables', 'OrderAddSiteId');
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
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Adding service ID to order base service table', 0);
		$this->_status( '' );

		$table = 'mshop_order_base_service';

		$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

		if ( $this->_schema->tableExists( $table ) === true &&
			$this->_schema->columnExists( $table, 'servid' ) === false )
		{
			$this->_executeList( $stmts );
			$this->_status( 'added' );
		} else {
			$this->_status( 'OK' );
		}
	}
}