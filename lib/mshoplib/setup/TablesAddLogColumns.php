<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds mtime, ctime and editor columns to all tables.
 */
class TablesAddLogColumns extends \Aimeos\MW\Setup\Task\Base
{
	private $mysqlProductUser = 'ALTER TABLE "mshop_product" CHANGE "user" "editor" VARCHAR(255) NOT NULL';

	private $mysql = array(
		// attribute
		'mshop_attribute' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_attribute" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_attribute" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_attribute" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_attribute" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_attribute" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),
		'mshop_attribute_list' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_attribute_list" ADD "mtime" DATETIME NOT NULL AFTER "pos"',
				'UPDATE "mshop_attribute_list" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_attribute_list" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_attribute_list" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_attribute_list" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),
		'mshop_attribute_list_type' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_attribute_list_type" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_attribute_list_type" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_attribute_list_type" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_attribute_list_type" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_attribute_list_type" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),
		'mshop_attribute_type' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_attribute_type" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_attribute_type" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_attribute_type" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_attribute_type" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_attribute_type" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		// catalog
		'mshop_catalog' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_catalog" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_catalog" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_catalog" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_catalog" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_catalog" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_catalog_index_attribute' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_catalog_index_attribute" ADD "mtime" DATETIME NOT NULL AFTER "listtype"',
				'UPDATE "mshop_catalog_index_attribute" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_catalog_index_attribute" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_catalog_index_attribute" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_catalog_index_attribute" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_catalog_index_catalog' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_catalog_index_catalog" ADD "mtime" DATETIME NOT NULL AFTER "pos"',
				'UPDATE "mshop_catalog_index_catalog" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_catalog_index_catalog" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_catalog_index_catalog" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_catalog_index_catalog" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_catalog_index_price' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_catalog_index_price" ADD "mtime" DATETIME NOT NULL AFTER "quantity"',
				'UPDATE "mshop_catalog_index_price" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_catalog_index_price" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_catalog_index_price" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_catalog_index_price" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_catalog_index_text' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_catalog_index_text" ADD "mtime" DATETIME NOT NULL AFTER "value"',
				'UPDATE "mshop_catalog_index_text" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_catalog_index_text" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_catalog_index_text" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_catalog_index_text" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_catalog_list' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_catalog_list" ADD "mtime" DATETIME NOT NULL AFTER "pos"',
				'UPDATE "mshop_catalog_list" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_catalog_list" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_catalog_list" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_catalog_list" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_catalog_list_type' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_catalog_list_type" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_catalog_list_type" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_catalog_list_type" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_catalog_list_type" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_catalog_list_type" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		// customer
		'mshop_customer' => array(
			'editor' => array(
				'ALTER TABLE "mshop_customer" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_customer_address' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_customer_address" ADD "mtime" DATETIME NOT NULL AFTER "pos"',
				'UPDATE "mshop_customer_address" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_customer_address" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_customer_address" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_customer_address" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_customer_list' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_customer_list" ADD "mtime" DATETIME NOT NULL AFTER "pos"',
				'UPDATE "mshop_customer_list" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_customer_list" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_customer_list" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_customer_list" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_customer_list_type' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_customer_list_type" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_customer_list_type" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_customer_list_type" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_customer_list_type" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_customer_list_type" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		// locale
		'mshop_locale' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_locale" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_locale" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_locale" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_locale" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_locale" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_locale_currency' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_locale_currency" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_locale_currency" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_locale_currency" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_locale_currency" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_locale_currency" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_locale_language' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_locale_language" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_locale_language" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_locale_language" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_locale_language" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_locale_language" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),

		),

		'mshop_locale_site' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_locale_site" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_locale_site" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_locale_site" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_locale_site" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_locale_site" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		// media
		'mshop_media' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_media" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_media" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_media" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_media" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_media" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_media_list' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_media_list" ADD "mtime" DATETIME NOT NULL AFTER "pos"',
				'UPDATE "mshop_media_list" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_media_list" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_media_list" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_media_list" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_media_list_type' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_media_list_type" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_media_list_type" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_media_list_type" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_media_list_type" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_media_list_type" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_media_type' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_media_type" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_media_type" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_media_type" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_media_type" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_media_type" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		// order
		'mshop_order' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_order" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_order" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_order" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_order_base' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_order_base" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_order_base" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_order_base" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_order_base_address' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_order_base_address" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_order_base_address" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_order_base_address" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_order_base_product' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_order_base_product" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_order_base_product" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_order_base_product" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_order_base_product_attr' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_order_base_product_attr" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_order_base_product_attr" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_order_base_product_attr" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_order_base_service' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_order_base_service" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_order_base_service" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_order_base_service" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_order_base_service_attr' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_order_base_service_attr" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_order_base_service_attr" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_order_base_service_attr" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		// plugin
		'mshop_plugin' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_plugin" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_plugin" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_plugin" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_plugin" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_plugin" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		'mshop_plugin_type' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_plugin_type" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_plugin_type" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_plugin_type" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_plugin_type" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_plugin_type" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		// price
		'mshop_price' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_price" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_price" SET "mtime"=NOW()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_price" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_price" SET "ctime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_price" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),
		'mshop_price_type' => array(
			'mtime' => array(
				'ALTER TABLE "mshop_price_type" ADD "mtime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_price_type" SET "mtime" = now()',
			),
			'ctime' => array(
				'ALTER TABLE "mshop_price_type" ADD "ctime" DATETIME NOT NULL AFTER "mtime"',
				'UPDATE "mshop_price_type" SET "ctime" = now()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_price_type" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "ctime"',
			),
		),

		// product
		'mshop_product' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_product" ADD "ctime" DATETIME NOT NULL AFTER "end"',
				'UPDATE "mshop_product" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_product" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_product" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_product" ADD "editor" VARCHAR(255) NOT NULL DEFAULT \'\' AFTER "mtime"',
			),
		),

		'mshop_product_list' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_product_list" ADD "ctime" DATETIME NOT NULL AFTER "pos"',
				'UPDATE "mshop_product_list" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_product_list" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_product_list" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_product_list" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),

		'mshop_product_list_type' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_product_list_type" ADD "ctime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_product_list_type" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_product_list_type" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_product_list_type" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_product_list_type" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),

		'mshop_product_stock' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_product_stock" ADD "ctime" DATETIME NOT NULL AFTER "backdate"',
				'UPDATE "mshop_product_stock" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_product_stock" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_product_stock" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_product_stock" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),

		'mshop_product_stock_warehouse' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_product_stock_warehouse" ADD "ctime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_product_stock_warehouse" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_product_stock_warehouse" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_product_stock_warehouse" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_product_stock_warehouse" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),

		'mshop_product_tag' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_product_tag" ADD "ctime" DATETIME NOT NULL AFTER "label"',
				'UPDATE "mshop_product_tag" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_product_tag" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_product_tag" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_product_tag" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),

		'mshop_product_tag_type' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_product_tag_type" ADD "ctime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_product_tag_type" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_product_tag_type" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_product_tag_type" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_product_tag_type" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),

		'mshop_product_type' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_product_type" ADD "ctime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_product_type" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_product_type" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_product_type" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_product_type" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),

		'mshop_service' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_service" ADD "ctime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_service" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_service" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_service" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_service" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),

		'mshop_service_list' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_service_list" ADD "ctime" DATETIME NOT NULL AFTER "pos"',
				'UPDATE "mshop_service_list" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_service_list" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_service" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_service_list" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),

		'mshop_service_list_type' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_service_list_type" ADD "ctime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_service_list_type" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_service_list_type" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_service_list_type" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_service_list_type" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),

		'mshop_service_type' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_service_type" ADD "ctime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_service_type" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_service_type" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_service_type" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_service_type" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),

		'mshop_supplier' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_supplier" ADD "ctime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_supplier" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_supplier" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_supplier" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_supplier" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),

		'mshop_supplier_address' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_supplier_address" ADD "ctime" DATETIME NOT NULL AFTER "pos"',
				'UPDATE "mshop_supplier_address" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_supplier_address" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_supplier_address" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_supplier_address" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),

		'mshop_text' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_text" ADD "ctime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_text" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_text" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_text" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_text" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),

		'mshop_text_list' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_text_list" ADD "ctime" DATETIME NOT NULL AFTER "pos"',
				'UPDATE "mshop_text_list" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_text_list" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_text_list" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_text_list" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),

		'mshop_text_list_type' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_text_list_type" ADD "ctime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_text_list_type" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_text_list_type" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_text_list_type" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_text_list_type" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),

		'mshop_text_type' => array(
			'ctime' => array(
				'ALTER TABLE "mshop_text_type" ADD "ctime" DATETIME NOT NULL AFTER "status"',
				'UPDATE "mshop_text_type" SET "ctime"=NOW()',
			),
			'mtime' => array(
				'ALTER TABLE "mshop_text_type" ADD "mtime" DATETIME NOT NULL AFTER "ctime"',
				'UPDATE "mshop_text_type" SET "mtime"=NOW()',
			),
			'editor' => array(
				'ALTER TABLE "mshop_text_type" ADD "editor" VARCHAR(255) NOT NULL AFTER "mtime"',
			),
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductWarehouseRenameTable' );
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
	 * Add column to table if it doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( $stmts )
	{
		$this->msg( 'Adding ctime/mtime/user columns to tables', 0 );
		$this->status( '' );

		if( $this->schema->tableExists( 'mshop_product' ) && $this->schema->columnExists( 'mshop_product', 'user' ) )
		{
			$this->msg( sprintf( 'Checking table "%2$s" for column "%1$s": ', 'user', 'mshop_product' ), 1 );

			$this->execute( $this->mysqlProductUser );
			$this->status( 'migrated' );
		}

		foreach( $stmts as $table=>$colList )
		{
			if( $this->schema->tableExists( $table ) === true )
			{
				foreach( $colList as $column=>$stmtList )
				{
					$this->msg( sprintf( 'Checking table "%2$s" for column "%1$s": ', $column, $table ), 1 );

					if( $this->schema->columnExists( $table, $column ) === false )
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
	}
}