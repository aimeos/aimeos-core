<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds the new type columns
 */
class TypesMigrateColumns extends \Aimeos\MW\Setup\Task\Base
{
	private $tables = [
		'db-attribute' => ['mshop_attribute', 'mshop_attribute_list', 'mshop_attribute_property'],
		'db-catalog' => ['mshop_catalog_list'],
		'db-customer' => ['mshop_customer_list', 'mshop_customer_property'],
		'db-media' => ['mshop_media', 'mshop_media_list', 'mshop_media_property'],
		'db-plugin' => ['mshop_plugin'],
		'db-price' => ['mshop_price', 'mshop_price_list'],
		'db-product' => ['mshop_product', 'mshop_product_list', 'mshop_product_property'],
		'db-service' => ['mshop_service', 'mshop_service_list'],
		'db-stock' => ['mshop_stock'],
		'db-supplier' => ['mshop_supplier_list'],
		'db-tag' => ['mshop_tag'],
		'db-text' => ['mshop_text', 'mshop_text_list'],
	];

	private $constraints = [
		'db-attribute' => ['mshop_attribute' => 'unq_msattr_sid_dom_cod_tid', 'mshop_attribute_list' => 'unq_msattli_sid_dm_rid_tid_pid', 'mshop_attribute_property' => 'unq_msattpr_sid_tid_lid_value'],
		'db-catalog' => ['mshop_catalog_list' => 'unq_mscatli_sid_dm_rid_tid_pid'],
		'db-customer' => ['mshop_customer_list' => 'unq_mscusli_sid_dm_rid_tid_pid', 'mshop_customer_property' => 'unq_mcuspr_sid_tid_lid_value'],
		'db-media' => ['mshop_media_list' => 'unq_msmedli_sid_dm_rid_tid_pid', 'mshop_media_property' => 'unq_msmedpr_sid_tid_lid_value'],
		'db-plugin' => ['mshop_plugin' => 'unq_msplu_sid_tid_prov'],
		'db-price' => ['mshop_price_list' => 'unq_msprili_sid_dm_rid_tid_pid'],
		'db-product' => ['mshop_product_list' => 'unq_msproli_sid_dm_rid_tid_pid', 'mshop_product_property' => 'unq_mspropr_sid_tid_lid_value'],
		'db-service' => ['mshop_service_list' => 'unq_msserli_sid_dm_rid_tid_pid'],
		'db-stock' => ['mshop_stock' => 'unq_mssto_sid_pcode_tid'],
		'db-supplier' => ['mshop_supplier_list' => 'unq_mssupli_sid_dm_rid_tid_pid'],
		'db-tag' => ['mshop_tag' => 'unq_mstag_sid_dom_tid_lid_lab'],
		'db-text' => ['mshop_text_list' => 'unq_mstexli_sid_dm_rid_tid_pid'],
	];

	private $migrations = [
		'db-attribute' => [
			'mshop_attribute' => 'UPDATE "mshop_attribute" SET "type" = ( SELECT "code" FROM "mshop_attribute_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
			'mshop_attribute_list' => 'UPDATE "mshop_attribute_list" SET "type" = ( SELECT "code" FROM "mshop_attribute_list_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
			'mshop_attribute_property' => 'UPDATE "mshop_attribute_property" SET "type" = ( SELECT "code" FROM "mshop_attribute_property_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
		],
		'db-catalog' => [
			'mshop_catalog_list' => 'UPDATE "mshop_catalog_list" SET "type" = ( SELECT "code" FROM "mshop_catalog_list_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
		],
		'db-customer' => [
			'mshop_customer_list' => 'UPDATE "mshop_customer_list" SET "type" = ( SELECT "code" FROM "mshop_customer_list_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
			'mshop_customer_property' => 'UPDATE "mshop_customer_property" SET "type" = ( SELECT "code" FROM "mshop_customer_property_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
		],
		'db-media' => [
			'mshop_media' => 'UPDATE "mshop_media" SET "type" = ( SELECT "code" FROM "mshop_media_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
			'mshop_media_list' => 'UPDATE "mshop_media_list" SET "type" = ( SELECT "code" FROM "mshop_media_list_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
			'mshop_media_property' => 'UPDATE "mshop_media_property" SET "type" = ( SELECT "code" FROM "mshop_media_property_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
		],
		'db-plugin' => [
			'mshop_plugin' => 'UPDATE "mshop_plugin" SET "type" = ( SELECT "code" FROM "mshop_plugin_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
		],
		'db-price' => [
			'mshop_price' => 'UPDATE "mshop_price" SET "type" = ( SELECT "code" FROM "mshop_price_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
			'mshop_price_list' => 'UPDATE "mshop_price_list" SET "type" = ( SELECT "code" FROM "mshop_price_list_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
		],
		'db-product' => [
			'mshop_product' => 'UPDATE "mshop_product" SET "type" = ( SELECT "code" FROM "mshop_product_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
			'mshop_product_list' => 'UPDATE "mshop_product_list" SET "type" = ( SELECT "code" FROM "mshop_product_list_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
			'mshop_product_property' => 'UPDATE "mshop_product_property" SET "type" = ( SELECT "code" FROM "mshop_product_property_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
		],
		'db-service' => [
			'mshop_service' => 'UPDATE "mshop_service" SET "type" = ( SELECT "code" FROM "mshop_service_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
			'mshop_service_list' => 'UPDATE "mshop_service_list" SET "type" = ( SELECT "code" FROM "mshop_service_list_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
		],
		'db-stock' => [
			'mshop_stock' => 'UPDATE "mshop_stock" SET "type" = ( SELECT "code" FROM "mshop_stock_type" AS t WHERE t."id" = "typeid" ) WHERE "type" = \'\'',
		],
		'db-supplier' => [
			'mshop_supplier_list' => 'UPDATE "mshop_supplier_list" SET "type" = ( SELECT "code" FROM "mshop_supplier_list_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
		],
		'db-tag' => [
			'mshop_tag' => 'UPDATE "mshop_tag" SET "type" = ( SELECT "code" FROM "mshop_tag_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
		],
		'db-text' => [
			'mshop_text' => 'UPDATE "mshop_text" SET "type" = ( SELECT "code" FROM "mshop_text_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
			'mshop_text_list' => 'UPDATE "mshop_text_list" SET "type" = ( SELECT "code" FROM "mshop_text_list_type" AS t WHERE t."id" = "typeid" AND t."domain" = "domain" ) WHERE "type" = \'\'',
		],
	];


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Executes the task
	 */
	public function migrate()
	{
		$this->msg( sprintf( 'Add new type columns' ), 0 );
		$this->status( '' );

		foreach( $this->tables as $rname => $list ) {
			$this->addColumn( $rname, $list );
		}

		$this->msg( sprintf( 'Drop old unique indexes' ), 0 );
		$this->status( '' );

		foreach( $this->constraints as $rname => $list ) {
			$this->dropIndex( $rname, $list );
		}

		$this->msg( sprintf( 'Migrate typeid to type' ), 0 );
		$this->status( '' );

		foreach( $this->migrations as $rname => $list ) {
			$this->migrateData( $rname, $list );
		}
	}


	protected function addColumn( $rname, $tables )
	{
		$conn = $this->acquire( $rname );
		$dbal = $conn->getRawObject();

		if( !( $dbal instanceof \Doctrine\DBAL\Connection ) ) {
			throw new \Aimeos\MW\Setup\Exception( 'Not a DBAL connection' );
		}

		$dbalManager = $dbal->getSchemaManager();
		$platform = $dbal->getDatabasePlatform();
		$config = $dbalManager->createSchemaConfig();

		foreach( $tables as $name )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $name ), 1 );

			if( $dbalManager->tablesExist( [$name] )
				&& ( $tableDef = $dbalManager->listTableDetails( $name ) )->hasColumn( 'type' ) === false
			) {
				$beforeSchema = new \Doctrine\DBAL\Schema\Schema( [clone $tableDef], [], $config );
				$tableDef->addColumn( 'type', 'string', ['length' => 64, 'default' => ''] );
				$afterSchema = new \Doctrine\DBAL\Schema\Schema( [$tableDef], [], $config );

				$schemaDiff = \Doctrine\DBAL\Schema\Comparator::compareSchemas( $beforeSchema, $afterSchema );

				$this->executeList( $schemaDiff->toSaveSql( $platform ), $rname );
				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}

		$this->release( $conn, $rname );
	}


	protected function dropIndex( $rname, $indexes )
	{
		$conn = $this->acquire( $rname );
		$dbal = $conn->getRawObject();

		if( !( $dbal instanceof \Doctrine\DBAL\Connection ) ) {
			throw new \Aimeos\MW\Setup\Exception( 'Not a DBAL connection' );
		}

		$dbalManager = $dbal->getSchemaManager();
		$platform = $dbal->getDatabasePlatform();
		$config = $dbalManager->createSchemaConfig();

		foreach( $indexes as $table => $name )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $dbalManager->tablesExist( [$table] )
				&& ( $tableDef = $dbalManager->listTableDetails( $table ) )->hasIndex( $name ) === true
			) {
				$beforeSchema = new \Doctrine\DBAL\Schema\Schema( [clone $tableDef], [], $config );
				$tableDef->dropIndex( $name );
				$afterSchema = new \Doctrine\DBAL\Schema\Schema( [$tableDef], [], $config );

				$schemaDiff = \Doctrine\DBAL\Schema\Comparator::compareSchemas( $beforeSchema, $afterSchema );

				$this->executeList( $schemaDiff->toSaveSql( $platform ), $rname );
				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}

		$this->release( $conn, $rname );
	}


	protected function migrateData( $rname, $stmts )
	{
		$schema = $this->getSchema( $rname );

		foreach( $stmts as $table => $stmt )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $schema->columnExists( $table, 'typeid' ) ) {
				$this->execute( $stmt, $rname );
				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
