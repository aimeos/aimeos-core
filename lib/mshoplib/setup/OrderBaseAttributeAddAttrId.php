<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds attrid column to mshop_order_base_*_attr tables.
 */
class MW_Setup_Task_OrderBaseAttributeAddAttrId extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_order_base_product_attr' => 'ALTER TABLE "mshop_order_base_product_attr" ADD "attrid" VARCHAR(32) NOT NULL COLLATE utf8_bin AFTER "siteid"',
		'mshop_order_base_service_attr' => 'ALTER TABLE "mshop_order_base_service_attr" ADD "attrid" VARCHAR(32) NOT NULL COLLATE utf8_bin AFTER "siteid"',
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
		$this->_process($this->_mysql);
	}


	/**
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Adding attrid column to order attribute tables', 0 );
		$this->_status('');

		foreach( $stmts as $table => $stmt )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true &&
				$this->_schema->columnExists( $table, 'attrid' ) === false
			) {
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