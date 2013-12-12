<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds status column to list tables and sets it to enable.
 */
class MW_Setup_Task_ListsAddStatus extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'add' => array(
			'mshop_attribute_list' => 'ALTER TABLE "mshop_attribute_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
			'mshop_catalog_list' => 'ALTER TABLE "mshop_catalog_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
			'mshop_customer_list' => 'ALTER TABLE "mshop_customer_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
			'mshop_media_list' => 'ALTER TABLE "mshop_media_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
			'mshop_price_list' => 'ALTER TABLE "mshop_price_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
			'mshop_product_list' => 'ALTER TABLE "mshop_product_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
			'mshop_service_list' => 'ALTER TABLE "mshop_service_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
			'mshop_text_list' => 'ALTER TABLE "mshop_text_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
		),
		'drop' => array(
			'mshop_attribute_list' => array(
				'idx_msattli_sid_start_end' => 'ALTER TABLE "mshop_attribute_list" DROP INDEX "idx_msattli_sid_start_end"',
			),
			'mshop_catalog_list' => array(
				'idx_mscatli_sid_start_end' => 'ALTER TABLE "mshop_catalog_list" DROP INDEX "idx_mscatli_sid_start_end"',
			),
			'mshop_customer_list' => array(
				'idx_mscusli_sid_start_end' => 'ALTER TABLE "mshop_customer_list" DROP INDEX "idx_mscusli_sid_start_end"',
			),
			'mshop_media_list' => array(
				'idx_msmedli_sid_start_end' => 'ALTER TABLE "mshop_media_list" DROP INDEX "idx_msmedli_sid_start_end"',
			),
			'mshop_price_list' => array(
				'idx_msprili_sid_start_end' => 'ALTER TABLE "mshop_price_list" DROP INDEX "idx_msprili_sid_start_end"',
			),
			'mshop_product_list' => array(
				'idx_msproli_sid_start_end' => 'ALTER TABLE "mshop_product_list" DROP INDEX "idx_msproli_sid_start_end"',
			),
			'mshop_service_list' => array(
				'idx_msserli_sid_start_end' => 'ALTER TABLE "mshop_service_list" DROP INDEX "idx_msserli_sid_start_end"',
			),
			'mshop_text_list' => array(
				'idx_mstexli_sid_start_end' => 'ALTER TABLE "mshop_text_list" DROP INDEX "idx_mstexli_sid_start_end"',
			),
		)
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
	 * Add column status to mshop_*_list tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Adding status column to all list tables', 0 );
		$this->_status( '' );

		foreach( $stmts['add'] as $table => $stmt )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true
				&& $this->_schema->columnExists( $table, 'status' ) === false )
			{
				$this->_execute( $stmt );
				$this->_status( 'added' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}

		foreach( $stmts['drop'] as $table => $indexes )
		{
			foreach ( $indexes AS $index => $stmt )
			{
				$this->_msg(sprintf('Checking index "%1$s": ', $index), 1);

				if( $this->_schema->tableExists( $table ) === true
					&& $this->_schema->indexExists( $table, $index ) === true )
				{
					$this->_execute( $stmt );
					$this->_status( 'dropped' );
				}
				else
				{
					$this->_status( 'OK' );
				}
			}
		}
	}
}