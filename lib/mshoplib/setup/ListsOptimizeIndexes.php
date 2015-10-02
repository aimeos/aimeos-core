<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Optimizes the list unique constraints and indexes.
 */
class MW_Setup_Task_ListsOptimizeIndexes extends MW_Setup_Task_Abstract
{
	private $mysql = array(
		'add' => array(
			'mshop_attribute_list' => array(
				'unq_msattli_sid_dm_rid_tid_pid' => 'ALTER TABLE "mshop_attribute_list" ADD CONSTRAINT "unq_msattli_sid_dm_rid_tid_pid" UNIQUE ("siteid", "domain", "refid", "typeid", "parentid")',
			),
			'mshop_catalog_list' => array(
				'unq_mscatli_sid_dm_rid_tid_pid' => 'ALTER TABLE "mshop_catalog_list" ADD CONSTRAINT "unq_mscatli_sid_dm_rid_tid_pid" UNIQUE ("siteid", "domain", "refid", "typeid", "parentid")',
			),
			'mshop_customer_list' => array(
				'unq_mscusli_sid_dm_rid_tid_pid' => 'ALTER TABLE "mshop_customer_list" ADD CONSTRAINT "unq_mscusli_sid_dm_rid_tid_pid" UNIQUE ("siteid", "domain", "refid", "typeid", "parentid")',
			),
			'mshop_media_list' => array(
				'unq_msmedli_sid_dm_rid_tid_pid' => 'ALTER TABLE "mshop_media_list" ADD CONSTRAINT "unq_msmedli_sid_dm_rid_tid_pid" UNIQUE ("siteid", "domain", "refid", "typeid", "parentid")',
			),
			'mshop_price_list' => array(
				'unq_msprili_sid_dm_rid_tid_pid' => 'ALTER TABLE "mshop_price_list" ADD CONSTRAINT "unq_msprili_sid_dm_rid_tid_pid" UNIQUE ("siteid", "domain", "refid", "typeid", "parentid")',
			),
			'mshop_product_list' => array(
				'unq_msproli_sid_dm_rid_tid_pid' => 'ALTER TABLE "mshop_product_list" ADD CONSTRAINT "unq_msproli_sid_dm_rid_tid_pid" UNIQUE ("siteid", "domain", "refid", "typeid", "parentid")',
			),
			'mshop_service_list' => array(
				'unq_msserli_sid_dm_rid_tid_pid' => 'ALTER TABLE "mshop_service_list" ADD CONSTRAINT "unq_msserli_sid_dm_rid_tid_pid" UNIQUE ("siteid", "domain", "refid", "typeid", "parentid")',
			),
			'mshop_text_list' => array(
				'unq_mstexli_sid_dm_rid_tid_pid' => 'ALTER TABLE "mshop_text_list" ADD CONSTRAINT "unq_mstexli_sid_dm_rid_tid_pid" UNIQUE ("siteid", "domain", "refid", "typeid", "parentid")',
			),
		),
		'delete' => array(
			'mshop_attribute_list' => array(
				'unq_msattli_sid_pid_dm_rid_tid' => 'ALTER TABLE "mshop_attribute_list" DROP INDEX "unq_msattli_sid_pid_dm_rid_tid"',
			),
			'mshop_catalog_list' => array(
				'unq_mscatli_sid_pid_dm_rid_tid' => 'ALTER TABLE "mshop_catalog_list" DROP INDEX "unq_mscatli_sid_pid_dm_rid_tid"',
			),
			'mshop_customer_list' => array(
				'unq_mscusli_sid_pid_dm_rid_tid' => 'ALTER TABLE "mshop_customer_list" DROP INDEX "unq_mscusli_sid_pid_dm_rid_tid"',
			),
			'mshop_media_list' => array(
				'unq_msmedli_sid_pid_dm_rid_tid' => 'ALTER TABLE "mshop_media_list" DROP INDEX "unq_msmedli_sid_pid_dm_rid_tid"',
			),
			'mshop_price_list' => array(
				'unq_msprili_sid_pid_dm_rid_tid' => 'ALTER TABLE "mshop_price_list" DROP INDEX "unq_msprili_sid_pid_dm_rid_tid"',
			),
			'mshop_product_list' => array(
				'unq_msproli_sid_pid_dm_rid_tid' => 'ALTER TABLE "mshop_product_list" DROP INDEX "unq_msproli_sid_pid_dm_rid_tid"',
			),
			'mshop_service_list' => array(
				'unq_msserli_sid_pid_dm_rid_tid' => 'ALTER TABLE "mshop_service_list" DROP INDEX "unq_msserli_sid_pid_dm_rid_tid"',
			),
			'mshop_text_list' => array(
				'unq_mstexli_sid_pid_dm_rid_tid' => 'ALTER TABLE "mshop_text_list" DROP INDEX "unq_mstexli_sid_pid_dm_rid_tid"',
			),
		),
		'indexes' => array(
			'mshop_attribute_list' => array(
				'idx_msattli_sid_rid_dom_tid' => 'ALTER TABLE "mshop_attribute_list" DROP INDEX "idx_msattli_sid_rid_dom_tid"',
				'idx_msattli_pid_sid_rid' => 'ALTER TABLE "mshop_attribute_list" DROP INDEX "idx_msattli_pid_sid_rid"',
			),
			'mshop_catalog_list' => array(
				'idx_mscatli_sid_rid_dom_tid' => 'ALTER TABLE "mshop_catalog_list" DROP INDEX "idx_mscatli_sid_rid_dom_tid"',
				'idx_mscatli_pid_sid_rid' => 'ALTER TABLE "mshop_catalog_list" DROP INDEX "idx_mscatli_pid_sid_rid"',
			),
			'mshop_customer_list' => array(
				'idx_mscusli_sid_rid_dom_tid' => 'ALTER TABLE "mshop_customer_list" DROP INDEX "idx_mscusli_sid_rid_dom_tid"',
				'idx_mscusli_pid_sid_rid' => 'ALTER TABLE "mshop_customer_list" DROP INDEX "idx_mscusli_pid_sid_rid"',
			),
			'mshop_media_list' => array(
				'idx_msmedli_sid_rid_dom_tid' => 'ALTER TABLE "mshop_media_list" DROP INDEX "idx_msmedli_sid_rid_dom_tid"',
				'idx_msmedli_pid_sid_rid' => 'ALTER TABLE "mshop_media_list" DROP INDEX "idx_msmedli_pid_sid_rid"',
			),
			'mshop_price_list' => array(
				'idx_msprili_sid_rid_dom_tid' => 'ALTER TABLE "mshop_price_list" DROP INDEX "idx_msprili_sid_rid_dom_tid"',
				'idx_msprili_pid_sid_rid' => 'ALTER TABLE "mshop_price_list" DROP INDEX "idx_msprili_pid_sid_rid"',
			),
			'mshop_product_list' => array(
				'idx_msproli_sid_rid_dom_tid' => 'ALTER TABLE "mshop_product_list" DROP INDEX "idx_msproli_sid_rid_dom_tid"',
				'idx_msproli_pid_sid_rid' => 'ALTER TABLE "mshop_product_list" DROP INDEX "idx_msproli_pid_sid_rid"',
			),
			'mshop_service_list' => array(
				'idx_msserli_sid_rid_dom_tid' => 'ALTER TABLE "mshop_service_list" DROP INDEX "idx_msserli_sid_rid_dom_tid"',
				'idx_msserli_pid_sid_rid' => 'ALTER TABLE "mshop_service_list" DROP INDEX "idx_msserli_pid_sid_rid"',
			),
			'mshop_text_list' => array(
				'idx_mstexli_sid_rid_dom_tid' => 'ALTER TABLE "mshop_text_list" DROP INDEX "idx_mstexli_sid_rid_dom_tid"',
				'idx_mstexli_pid_sid_rid' => 'ALTER TABLE "mshop_text_list" DROP INDEX "idx_mstexli_pid_sid_rid"',
			),
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
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
		return array(
			'AttributeModifyIndexes', 'CatalogModifyIndexes', 'CustomerModifyIndexes', 'MediaModifyIndexes',
			'ProductModifyIndexes', 'ServiceModifyIndexes', 'TextModifyIndexes', 'TablesCreateMShop'
		);
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Optimize list indexes', 0 ); $this->status( '' );

		foreach( $stmts['add'] as $table => $stmtList )
		{
			foreach( $stmtList as $name => $stmt )
			{
				$this->msg( sprintf( 'Adding constraint "%1$s": ', $name ), 1 );

				if( $this->schema->tableExists( $table ) === true
					&& $this->schema->constraintExists( $table, $name ) === false
				) {
					$this->execute( $stmt );
					$this->status( 'done' );
				} else {
					$this->status( 'OK' );
				}
			}
		}

		foreach( $stmts['delete'] as $table => $stmtList )
		{
			foreach( $stmtList as $name => $stmt )
			{
				$this->msg( sprintf( 'Deleting constraint "%1$s": ', $name ), 1 );

				if( $this->schema->tableExists( $table ) === true
					&& $this->schema->constraintExists( $table, $name ) === true
				) {
					$this->execute( $stmt );
					$this->status( 'done' );
				} else {
					$this->status( 'OK' );
				}
			}
		}

		foreach( $stmts['indexes'] as $table => $stmtList )
		{
			foreach( $stmtList as $name => $stmt )
			{
				$this->msg( sprintf( 'Dropping index "%1$s": ', $name ), 1 );

				if( $this->schema->tableExists( $table ) === true
					&& $this->schema->indexExists( $table, $name ) === true
				) {
					$this->execute( $stmt );
					$this->status( 'done' );
				} else {
					$this->status( 'OK' );
				}
			}
		}
	}
}
