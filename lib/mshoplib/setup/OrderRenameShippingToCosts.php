<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Renames shipping column to costs the order tables.
 */
class MW_Setup_Task_OrderRenameShippingToCosts extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_order_base' => array(
			'ALTER TABLE "mshop_order_base" CHANGE "shipping" "costs" DECIMAL(12,2) NOT NULL',
		),

		'mshop_order_base_product' => array(
			'ALTER TABLE "mshop_order_base_product" CHANGE "shipping" "costs" DECIMAL(12,2) NOT NULL',
		),

		'mshop_order_base_service' => array(
			'ALTER TABLE "mshop_order_base_service" CHANGE "shipping" "costs" DECIMAL(12,2) NOT NULL',
		),
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
	 * Renames all shipping columns to costs if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Renaming shipping to costs', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) && $this->_schema->columnExists( $table, 'shipping' ) === true )
			{
				$this->_executeList( $stmtList );
				$this->_status( 'renamed' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}

}
