<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Drops old indexes in mshop_order_base_product_attr and mshop_order_base_service_attr.
 */
class MW_Setup_Task_OrderDropBaseAttrIndex extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_order_base_product_attr' => array(
			'idx_msordbaprat_sid_oid_cd_val' => 'ALTER TABLE "mshop_order_base_product_attr" DROP INDEX "idx_msordbaprat_sid_oid_cd_val"'
		),
		'mshop_order_base_service_attr' => array(
			'idx_msordbaseat_sid_oid_cd_val' => 'ALTER TABLE "mshop_order_base_service_attr" DROP INDEX "idx_msordbaseat_sid_oid_cd_val"'
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
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
		$this->_process($this->_mysql);
	}


	/**
	 * Drops old indexes in order attribute tables.
	 *
	 * @param array $stmts Associative list of tables names and SQL statements to execute
	 */
	protected function _process( array $stmts )
	{
		$this->_msg ('Drop old indexes in order attribute tables', 0);
		$this->_status('');

		foreach( $stmts AS $table => $stmts )
		{
			foreach( $stmts as $index => $stmt )
			{
				$this->_msg( sprintf( 'Checking index "%1$s"', $index ), 1 );

				if( $this->_schema->tableExists( $table ) === true
					&& $this->_schema->indexExists( $table, $index ) === true )
				{
					$this->_execute($stmt);
					$this->_status('dropped');
				}
				else
				{
					$this->_status('OK');
				}
			}
		}
	}
}
