<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Removes locale constraints from product tables.
 */
class MW_Setup_Task_ProductDropLocaleConstraints extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_product_list_type' => array(
			'fk_msprolity_siteid' => 'ALTER TABLE "mshop_product_list_type" DROP FOREIGN KEY "fk_msprolity_siteid"',
		),
		'mshop_product_list' => array(
			'fk_msproli_siteid' => 'ALTER TABLE "mshop_product_list" DROP FOREIGN KEY "fk_msproli_siteid"',
		),
		'mshop_product_type' => array(
			'fk_msproty_siteid' => 'ALTER TABLE "mshop_product_type" DROP FOREIGN KEY "fk_msproty_siteid"',
		),
		'mshop_product' => array(
			'fk_mspro_siteid' => 'ALTER TABLE "mshop_product" DROP FOREIGN KEY "fk_mspro_siteid"',
		),
		'mshop_product_tag_type' => array(
			'fk_msprotaty_siteid' => 'ALTER TABLE "mshop_product_tag_type" DROP FOREIGN KEY "fk_msprotaty_siteid"',
		),
		'mshop_product_tag' => array(
			'fk_msprota_siteid' => 'ALTER TABLE "mshop_product_tag" DROP FOREIGN KEY "fk_msprota_siteid"',
		),
		'mshop_product_stock_warehouse' => array(
			'fk_msprostwa_siteid' => 'ALTER TABLE "mshop_product_stock_warehouse" DROP FOREIGN KEY "fk_msprostwa_siteid"',
		),
		'mshop_product_stock' => array(
			'fk_msprost_siteid' => 'ALTER TABLE "mshop_product_stock" DROP FOREIGN KEY "fk_msprost_siteid"',
		),
	);




	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductWarehouseRenameTable' );
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
	 * Drops local constraints.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Removing locale constraints from product tables', 0 );
		$this->_status( '' );

		$schema = $this->_getSchema( 'db-product' );

		foreach( $stmts as $table => $list )
		{
			if( $schema->tableExists( $table ) === true )
			{
				foreach( $list as $constraint => $stmt )
				{
					$this->_msg( sprintf( 'Removing "%1$s" from "%2$s"', $constraint, $table ), 1 );

					if( $schema->constraintExists( $table, $constraint ) !== false )
					{
						$this->_execute( $stmt, 'db-product' );
						$this->_status( 'done' );
					} else {
						$this->_status( 'OK' );
					}
				}
			}
		}
	}
}