<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes collation of code columns.
 */
class ColumnCodeCollateToUtf8Bin extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_catalog' => 'ALTER TABLE "mshop_catalog" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_attribute_type' => 'ALTER TABLE "mshop_attribute_type" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_attribute' => 'ALTER TABLE "mshop_attribute" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_attribute_list_type' => 'ALTER TABLE "mshop_attribute_list_type" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_catalog_list_type' => 'ALTER TABLE "mshop_catalog_list_type" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_customer' => 'ALTER TABLE "mshop_customer" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_customer_list_type' => 'ALTER TABLE "mshop_customer_list_type" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_locale_site' => 'ALTER TABLE "mshop_locale_site" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_media_type' => 'ALTER TABLE "mshop_media_type" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_media_list_type' => 'ALTER TABLE "mshop_media_list_type" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_order_base_product_attr' => 'ALTER TABLE "mshop_order_base_product_attr" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_order_base_service' => 'ALTER TABLE "mshop_order_base_service" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_order_base_service_attr' => 'ALTER TABLE "mshop_order_base_service_attr" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_plugin_type' => 'ALTER TABLE "mshop_plugin_type" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_price_type' => 'ALTER TABLE "mshop_price_type" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_product_type' => 'ALTER TABLE "mshop_product_type" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_product' => 'ALTER TABLE "mshop_product" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_product_list_type' => 'ALTER TABLE "mshop_product_list_type" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_product_tag_type' => 'ALTER TABLE "mshop_product_tag_type" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_product_warehouse' => 'ALTER TABLE "mshop_product_warehouse" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_service_type' => 'ALTER TABLE "mshop_service_type" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_service' => 'ALTER TABLE "mshop_service" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_service_list_type' => 'ALTER TABLE "mshop_service_list_type" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_text_type' => 'ALTER TABLE "mshop_text_type" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_text_list_type' => 'ALTER TABLE "mshop_text_list_type" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL',
		'mshop_supplier' => 'ALTER TABLE "mshop_supplier" CHANGE "code" "code" VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL'
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogAddCode' );
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
	 * Migrates service text data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$column = 'code';
		$this->msg( 'Changing code columns', 0 ); $this->status( '' );

		foreach( $stmts as $table=>$stmt )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true
				&& $this->schema->columnExists( $table, $column ) === true
				&& $this->schema->getColumnDetails( $table, $column )->getCollationType() !== 'utf8_bin' )
			{
				$this->execute( $stmt );
				$this->status( 'migrated' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
