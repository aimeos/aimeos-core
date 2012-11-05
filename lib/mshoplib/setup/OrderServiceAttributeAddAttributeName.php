<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: OrderServiceAttributeAddAttributeName.php 14251 2011-12-09 13:36:27Z nsendetzky $
 */


/**
 * Adds name column to order base service attribute table.
 */
class MW_Setup_Task_OrderServiceAttributeAddAttributeName extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'ALTER TABLE "mshop_order_base_service_attr" ADD "name" VARCHAR(255) NOT NULL AFTER "value"',
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables' );
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
	 * Migrates service attribute data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Adding name to order service attribute table', 0 );

		if( $this->_schema->tableExists( 'mshop_order_base_service_attr' ) === true
			&& $this->_schema->columnExists( 'mshop_order_base_service_attr', 'name' ) === false )
		{
			$this->_executeList( $stmts );
			$this->_status( 'added' );
		}
		else
		{
			$this->_status( 'OK' );
		}
	}
}
