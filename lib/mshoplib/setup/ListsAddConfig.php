<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds config column to list tables.
 */
class MW_Setup_Task_ListsAddConfig extends MW_Setup_Task_Base
{
	private $mysql = array(
		'mshop_attribute_list' => 'ALTER TABLE "mshop_attribute_list" ADD "config" TEXT NOT NULL AFTER "end"',
		'mshop_catalog_list' => 'ALTER TABLE "mshop_catalog_list" ADD "config" TEXT NOT NULL AFTER "end"',
		'mshop_customer_list' => 'ALTER TABLE "mshop_customer_list" ADD "config" TEXT NOT NULL AFTER "end"',
		'mshop_media_list' => 'ALTER TABLE "mshop_media_list" ADD "config" TEXT NOT NULL AFTER "end"',
		'mshop_price_list' => 'ALTER TABLE "mshop_price_list" ADD "config" TEXT NOT NULL AFTER "end"',
		'mshop_product_list' => 'ALTER TABLE "mshop_product_list" ADD "config" TEXT NOT NULL AFTER "end"',
		'mshop_service_list' => 'ALTER TABLE "mshop_service_list" ADD "config" TEXT NOT NULL AFTER "end"',
		'mshop_text_list' => 'ALTER TABLE "mshop_text_list" ADD "config" TEXT NOT NULL AFTER "end"',
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
	 * Add column config to mshop_*_list tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Adding config column to all list tables', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true
				&& $this->schema->columnExists( $table, 'config' ) === false )
			{
				$this->execute( $stmt );
				$this->status( 'added' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}