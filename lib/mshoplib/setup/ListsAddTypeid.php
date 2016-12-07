<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds typeid column to list tables and migrates data in type column.
 */
class ListsAddTypeid extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_attribute_list' => array(
			'ALTER TABLE "mshop_attribute_list" ADD "typeid" INTEGER DEFAULT NULL AFTER "siteid"',
			'ALTER TABLE "mshop_attribute_list" ADD CONSTRAINT "fk_msattli_typeid" FOREIGN KEY ( "typeid" ) REFERENCES "mshop_attribute_list_type" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
			'INSERT INTO "mshop_attribute_list_type" ( "code", "siteid" ) SELECT DISTINCT "type", "siteid" FROM "mshop_attribute_list"',
			'UPDATE "mshop_attribute_list" l, "mshop_attribute_list_type" lt SET "typeid" = lt."id" WHERE lt."code" = l."type" AND lt."siteid" = l."siteid"',
			'UPDATE "mshop_attribute_list" l, "mshop_attribute_list_type" lt SET "typeid" = lt."id" WHERE lt."code" = l."type" AND lt."siteid" IS NULL AND l."siteid" IS NULL',
			'ALTER TABLE "mshop_attribute_list" DROP "type"',
		),
		'mshop_attribute_option_list' => array(
			'ALTER TABLE "mshop_attribute_option_list" ADD "typeid" INTEGER DEFAULT NULL AFTER "siteid"',
			'ALTER TABLE "mshop_attribute_option_list" ADD CONSTRAINT "fk_msattopli_typeid" FOREIGN KEY ( "typeid" ) REFERENCES "mshop_attribute_option_list_type" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
			'INSERT INTO "mshop_attribute_option_list_type" ( "code", "siteid" ) SELECT DISTINCT "type", "siteid" FROM "mshop_attribute_option_list"',
			'UPDATE "mshop_attribute_option_list" l, "mshop_attribute_option_list_type" lt SET "typeid" = lt."id" WHERE lt."code" = l."type" AND lt."siteid" = l."siteid"',
			'UPDATE "mshop_attribute_option_list" l, "mshop_attribute_option_list_type" lt SET "typeid" = lt."id" WHERE lt."code" = l."type" AND lt."siteid" IS NULL AND l."siteid" IS NULL',
			'ALTER TABLE "mshop_attribute_option_list" DROP "type"',
		),
		'mshop_catalog_list' => array(
			'ALTER TABLE "mshop_catalog_list" ADD "typeid" INTEGER DEFAULT NULL AFTER "siteid"',
			'ALTER TABLE "mshop_catalog_list" ADD CONSTRAINT "fk_mscatli_typeid" FOREIGN KEY ( "typeid" ) REFERENCES "mshop_catalog_list_type" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
			'INSERT INTO "mshop_catalog_list_type" ( "code", "siteid" ) SELECT DISTINCT "type", "siteid" FROM "mshop_catalog_list"',
			'UPDATE "mshop_catalog_list" l, "mshop_catalog_list_type" lt SET "typeid" = lt."id" WHERE lt."code" = l."type" AND lt."siteid" = l."siteid"',
			'UPDATE "mshop_catalog_list" l, "mshop_catalog_list_type" lt SET "typeid" = lt."id" WHERE lt."code" = l."type" AND lt."siteid" IS NULL AND l."siteid" IS NULL',
			'ALTER TABLE "mshop_catalog_list" DROP "type"',
		),
		'mshop_product_list' => array(
			'ALTER TABLE "mshop_product_list" ADD "typeid" INTEGER DEFAULT NULL AFTER "siteid"',
			'ALTER TABLE "mshop_product_list" ADD CONSTRAINT "fk_msproli_typeid" FOREIGN KEY ( "typeid" ) REFERENCES "mshop_product_list_type" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
			'INSERT INTO "mshop_product_list_type" ( "code", "siteid" ) SELECT DISTINCT "type", "siteid" FROM "mshop_product_list"',
			'UPDATE "mshop_product_list" l, "mshop_product_list_type" lt SET "typeid" = lt."id" WHERE lt."code" = l."type" AND lt."siteid" = l."siteid"',
			'UPDATE "mshop_product_list" l, "mshop_product_list_type" lt SET "typeid" = lt."id" WHERE lt."code" = l."type" AND lt."siteid" IS NULL AND l."siteid" IS NULL',
			'ALTER TABLE "mshop_product_list" DROP "type"',
		),
		'mshop_service_list' =>  array(
			'ALTER TABLE "mshop_service_list" ADD "typeid" INTEGER DEFAULT NULL AFTER "siteid"',
			'ALTER TABLE "mshop_service_list" ADD CONSTRAINT "fk_msserli_typeid" FOREIGN KEY ( "typeid" ) REFERENCES "mshop_service_list_type" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
			'INSERT INTO "mshop_service_list_type" ( "code", "siteid" ) SELECT DISTINCT "type", "siteid" FROM "mshop_service_list"',
			'UPDATE "mshop_service_list" l, "mshop_service_list_type" lt SET "typeid" = lt."id" WHERE lt."code" = l."type" AND lt."siteid" = l."siteid"',
			'UPDATE "mshop_service_list" l, "mshop_service_list_type" lt SET "typeid" = lt."id" WHERE lt."code" = l."type" AND lt."siteid" IS NULL AND l."siteid" IS NULL',
			'ALTER TABLE "mshop_service_list" DROP "type"',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'MShopAddTypeData' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process( $this->mysql );
	}


	/**
	 * Add column typeid to mshop_*_list tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Move list types to separate table', 0 ); $this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true
				&& $this->schema->columnExists( $table, 'type' ) === true
				&& $this->schema->columnExists( $table, 'typeid' ) === false )
			{
				$this->executeList( $stmtList );
				$this->status( 'migrated' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}