<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds column type to tables mshop_order_base_product_attr and mshop_order_base_service_attr.
 */
class MW_Setup_Task_OrderAddBaseAttrAddType extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_order_base_product_attr' => 'ALTER TABLE "mshop_order_base_product_attr" ADD "type" VARCHAR(32) NOT NULL AFTER "ordprodid"',
		'mshop_order_base_service_attr' => 'ALTER TABLE "mshop_order_base_service_attr" ADD "type" VARCHAR(32) NOT NULL AFTER "ordservid"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTable' );
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
	 * @param array $stmts Associative list of table names and SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Add column type to order attribute tables', 0 );
		$this->_status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->_msg( sprintf( 'Checking "%1$s" table', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true
				&& $this->_schema->columnExists( $table, 'type' ) === false )
			{
				$this->_execute( $stmt );
				$this->_status( 'added' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}
