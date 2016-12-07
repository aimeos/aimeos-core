<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds site ID columns to order tables.
 */
class OrderAddSiteId extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
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
		return array( 'OrderRenameTables', 'OrderServiceAttributeAddPrimaryKey' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMShop', 'OrderAlterForeignKeyContraintsOnDelete' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Adding siteid to order tables', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true &&
				$this->schema->columnExists( $table, 'siteid' ) === false )
			{
				$this->executeList( $stmt );
				$this->status( 'added' );
			} else {
				$this->status( 'OK' );
			}
		}
	}
}