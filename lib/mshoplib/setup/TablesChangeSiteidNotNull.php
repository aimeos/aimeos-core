<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes site ID to NOT NULL in all tables.
 */
class TablesChangeSiteidNotNull extends \Aimeos\MW\Setup\Task\Base
{
	private $site = '
		INSERT INTO "mshop_locale_site" ("code", "label", "config", "status", "level", "nleft", "nright", "mtime", "ctime", "editor")
		SELECT \'default\', \'Default\', \'{}\', 1, 0, ( SELECT COALESCE( MAX("nright"), 0 ) FROM "mshop_locale_site" ) + 1, ( SELECT COALESCE( MAX("nright"), 0 ) FROM "mshop_locale_site" ) + 2, NOW(), NOW(), \'\' FROM DUAL
		WHERE ( SELECT COUNT(*) FROM "mshop_locale_site" WHERE "code" = \'default\' ) = 0
	';

	private $mysql = array(
		'madmin_job' => array(
			'UPDATE "madmin_job" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "madmin_job" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_attribute' => array(
			'UPDATE "mshop_attribute" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_attribute" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_attribute_list' => array(
			'UPDATE "mshop_attribute_list" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_attribute_list" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_attribute_list_type' => array(
			'UPDATE "mshop_attribute_list_type" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_attribute_list_type" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_attribute_type' => array(
			'UPDATE "mshop_attribute_type" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_attribute_type" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_catalog' => array(
			'UPDATE "mshop_catalog" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_catalog" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_catalog_index_attribute' => array(
			'UPDATE "mshop_catalog_index_attribute" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_catalog_index_attribute" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_catalog_index_catalog' => array(
			'UPDATE "mshop_catalog_index_catalog" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_catalog_index_catalog" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_catalog_index_price' => array(
			'UPDATE "mshop_catalog_index_price" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_catalog_index_price" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_catalog_index_text' => array(
			'UPDATE "mshop_catalog_index_text" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_catalog_index_text" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_catalog_list' => array(
			'UPDATE "mshop_catalog_list" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_catalog_list" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_catalog_list_type' => array(
			'UPDATE "mshop_catalog_list_type" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_catalog_list_type" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_coupon' => array(
			'UPDATE "mshop_coupon" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_coupon" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_coupon_code' => array(
			'UPDATE "mshop_coupon_code" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_coupon_code" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_customer_list' => array(
			'UPDATE "mshop_customer_list" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_customer_list" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_customer_list_type' => array(
			'UPDATE "mshop_customer_list_type" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_customer_list_type" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_locale' => array(
			'UPDATE "mshop_locale" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_locale" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_media' => array(
			'UPDATE "mshop_media" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_media" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_media_list' => array(
			'UPDATE "mshop_media_list" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_media_list" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_media_list_type' => array(
			'UPDATE "mshop_media_list_type" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_media_list_type" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_media_type' => array(
			'UPDATE "mshop_media_type" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_media_type" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_plugin' => array(
			'UPDATE "mshop_plugin" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_plugin" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_plugin_type' => array(
			'UPDATE "mshop_plugin_type" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_plugin_type" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_price' => array(
			'UPDATE "mshop_price" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_price" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_price_type' => array(
			'UPDATE "mshop_price_type" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_price_type" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_product' => array(
			'UPDATE "mshop_product" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_product" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_product_list' => array(
			'UPDATE "mshop_product_list" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_product_list" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_product_list_type' => array(
			'UPDATE "mshop_product_list_type" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_product_list_type" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_product_stock' => array(
			'UPDATE "mshop_product_stock" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_product_stock" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_product_stock_warehouse' => array(
			'UPDATE "mshop_product_stock_warehouse" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_product_stock_warehouse" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_product_tag' => array(
			'UPDATE "mshop_product_tag" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_product_tag" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_product_tag_type' => array(
			'UPDATE "mshop_product_tag_type" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_product_tag_type" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_product_type' => array(
			'UPDATE "mshop_product_type" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_product_type" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_service' => array(
			'UPDATE "mshop_service" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_service" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_service_list' => array(
			'UPDATE "mshop_service_list" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_service_list" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_service_list_type' => array(
			'UPDATE "mshop_service_list_type" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_service_list_type" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_service_type' => array(
			'UPDATE "mshop_service_type" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_service_type" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_supplier' => array(
			'UPDATE "mshop_supplier" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_supplier" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_supplier_address' => array(
			'UPDATE "mshop_supplier_address" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_supplier_address" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_text' => array(
			'UPDATE "mshop_text" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_text" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_text_list' => array(
			'UPDATE "mshop_text_list" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_text_list" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_text_list_type' => array(
			'UPDATE "mshop_text_list_type" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_text_list_type" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
		'mshop_text_type' => array(
			'UPDATE "mshop_text_type" SET "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) WHERE "siteid" IS NULL',
			'ALTER TABLE "mshop_text_type" CHANGE COLUMN "siteid" "siteid" INTEGER NOT NULL',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'LocaleChangeSitesToTree', 'TablesAddLogColumns', 'ProductStockExtendUniqueByWarehouseid' );
	}

	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMAdmin', 'TablesCreateMShop' );
	}

	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process( $this->mysql );
	}

	/**
	 * Changes site ID to NOT NULL and migrates existing entries.
	 *
	 * @param array $stmts Associative array of tables names and SQL statements.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Changing site ID to NOT NULL', 0 );
		$this->status( '' );

		if( $this->schema->tableExists( 'mshop_locale_site' ) ) {
			$this->execute( $this->site );
		}

		foreach( $stmts as $table => $stmtList )
		{
			$this->msg( sprintf( 'Changing table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) &&
				$this->schema->getColumnDetails( $table, 'siteid' )->isNullable() )
			{
				$this->executeList( $stmtList );
				$this->status( 'done' );
			} else {
				$this->status( 'OK' );
			}
		}
	}

}
