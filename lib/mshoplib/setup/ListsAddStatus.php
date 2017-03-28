<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds status column to list tables and sets it to enable.
 */
class ListsAddStatus extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_attribute_list' => array(
			'ALTER TABLE "mshop_attribute_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
			'ALTER TABLE "mshop_attribute_list" DROP INDEX "idx_msattli_sid_start_end"',
			'UPDATE "mshop_attribute_list" SET "status" = 1',
		),
		'mshop_catalog_list' => array(
			'ALTER TABLE "mshop_catalog_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
			'ALTER TABLE "mshop_catalog_list" DROP INDEX "idx_mscatli_sid_start_end"',
			'UPDATE "mshop_catalog_list" SET "status" = 1',
		),
		'mshop_customer_list' => array(
			'ALTER TABLE "mshop_customer_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
			'ALTER TABLE "mshop_customer_list" DROP INDEX "idx_mscusli_sid_start_end"',
			'UPDATE "mshop_customer_list" SET "status" = 1',
		),
		'mshop_media_list' => array(
			'ALTER TABLE "mshop_media_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
			'ALTER TABLE "mshop_media_list" DROP INDEX "idx_msmedli_sid_start_end"',
			'UPDATE "mshop_media_list" SET "status" = 1',
		),
		'mshop_price_list' => array(
			'ALTER TABLE "mshop_price_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
			'ALTER TABLE "mshop_price_list" DROP INDEX "idx_msprili_sid_start_end"',
			'UPDATE "mshop_price_list" SET "status" = 1',
		),
		'mshop_product_list' => array(
			'ALTER TABLE "mshop_product_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
			'ALTER TABLE "mshop_product_list" DROP INDEX "idx_msproli_sid_start_end"',
			'UPDATE "mshop_product_list" SET "status" = 1',
		),
		'mshop_service_list' => array(
			'ALTER TABLE "mshop_service_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
			'ALTER TABLE "mshop_service_list" DROP INDEX "idx_msserli_sid_start_end"',
			'UPDATE "mshop_service_list" SET "status" = 1',
		),
		'mshop_text_list' => array(
			'ALTER TABLE "mshop_text_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
			'ALTER TABLE "mshop_text_list" DROP INDEX "idx_mstexli_sid_start_end"',
			'UPDATE "mshop_text_list" SET "status" = 1',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return [];
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
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Add column status to mshop_*_list tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Adding status column to all list tables', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true
				&& $this->schema->columnExists( $table, 'status' ) === false )
			{
				$this->executeList( $stmtList );
				$this->status( 'added' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}