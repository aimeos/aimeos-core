<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds product ID column to order base product tables.
 */
class MW_Setup_Task_OrderAddBaseProductProductid extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'ALTER TABLE "mshop_order_base_product" ADD "prodid" VARCHAR(32) NOT NULL COLLATE utf8_bin AFTER "siteid"',
		'UPDATE "mshop_order_base_product" SET "prodid" = ( SELECT p."id" FROM "mshop_product" p WHERE p."siteid" = "siteid" AND p."code" = "prodcode" LIMIT 1 ) WHERE "prodid" = \'\'',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables', 'OrderAddSiteId' );
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
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Adding product ID to order base product table', 0 );
		$this->_status( '' );

		$table = 'mshop_order_base_product';

		$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

		if( $this->_schema->tableExists( $table ) === true &&
			$this->_schema->columnExists( $table, 'prodid' ) === false )
		{
			$this->_executeList( $stmts );
			$this->_status( 'added' );
		} else {
			$this->_status( 'OK' );
		}
	}
}