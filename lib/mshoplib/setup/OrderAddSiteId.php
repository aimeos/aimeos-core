<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds site ID columns to order tables.
 */
class MW_Setup_Task_OrderAddSiteId extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_order_base' => array(
			'ALTER TABLE "mshop_order_base" ADD "siteid" INTEGER NULL AFTER "id"',
			'UPDATE "mshop_order_base" as t1, "mshop_global_site" as t3
				SET t1."siteid" = t3."id"
				WHERE t1."sitecode" = t3."code"
			'
		),
		'mshop_order' => array(
			'ALTER TABLE "mshop_order" ADD "siteid" INTEGER NULL AFTER "baseid"',
			'UPDATE "mshop_order" as t1, "mshop_order_base" as t2, "mshop_global_site" as t3
				SET t1.siteid = t3.id
				WHERE t2.sitecode = t3.code AND t2.id = t1.baseid
			'
		),
		'mshop_order_base_address' => array(
			'ALTER TABLE "mshop_order_base_address" ADD "siteid" INTEGER NULL AFTER "baseid"',
			'UPDATE "mshop_order_base_address" as t1, "mshop_order_base" as t2, "mshop_global_site" as t3
				SET t1.siteid = t3.id
				WHERE t2.sitecode = t3.code AND t2.id = t1.baseid
			'
		),
		'mshop_order_base_discount' => array(
			'ALTER TABLE "mshop_order_base_discount" ADD "siteid" INTEGER NULL AFTER "baseid"',
			'UPDATE "mshop_order_base_discount" as t1, "mshop_order_base" as t2, "mshop_global_site" as t3
				SET t1.siteid = t3.id
				WHERE t2.sitecode = t3.code AND t2.id = t1.baseid
			'
		),
		'mshop_order_base_product' => array(
			'ALTER TABLE "mshop_order_base_product" ADD "siteid" INTEGER NULL AFTER "baseid"',
			'UPDATE "mshop_order_base_product" as t1, "mshop_order_base" as t2, "mshop_global_site" as t3
				SET t1.siteid = t3.id
				WHERE t2.sitecode = t3.code AND t2.id = t1.baseid
			'
		),
		'mshop_order_base_service' => array(
			'ALTER TABLE "mshop_order_base_service" ADD "siteid" INTEGER NULL AFTER "baseid"',
			'UPDATE "mshop_order_base_service" as t1, "mshop_order_base" as t2, "mshop_global_site" as t3
				SET t1.siteid = t3.id
				WHERE t2.sitecode = t3.code AND t2.id = t1.baseid
			'
		),
		'mshop_order_base_product_attr' => array(
			'ALTER TABLE "mshop_order_base_product_attr" ADD "siteid" INTEGER NULL AFTER "id"',
			'UPDATE "mshop_order_base_product_attr" as t1, "mshop_order_base" as t2, "mshop_global_site" as t3,
				"mshop_order_base_product" as t4
				SET t1.siteid = t3.id
				WHERE t2.sitecode = t3.code AND t2.id = t4.baseid AND t1.ordprodid = t4.id
			'
		),
		'mshop_order_base_service_attr' => array(
			'ALTER TABLE "mshop_order_base_service_attr" ADD "siteid" INTEGER NULL AFTER "id"',
			'UPDATE "mshop_order_base_service_attr" as t1, "mshop_order_base" as t2, "mshop_global_site" as t3,
				"mshop_order_base_service" as t4
				SET t1.siteid = t3.id
				WHERE t2.sitecode = t3.code AND t2.id = t4.baseid AND t1.ordservid = t4.id
			'
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array('OrderRenameTables', 'OrderServiceAttributeAddPrimaryKey');
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array('TablesCreateMShop', 'OrderAlterForeignKeyContraintsOnDelete');
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
		$this->_msg( 'Adding siteid to order tables', 0);
		$this->_status( '' );

		foreach( $stmts AS $table => $stmt )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if ( $this->_schema->tableExists( $table ) === true &&
				$this->_schema->columnExists( $table, 'siteid' ) === false )
			{
				$this->_executeList( $stmt );
				$this->_status( 'added' );
			} else {
				$this->_status( 'OK' );
			}
		}
	}
}