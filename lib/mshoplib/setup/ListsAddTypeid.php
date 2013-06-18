<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds typeid column to list tables and migrates data in type column.
 */
class MW_Setup_Task_ListsAddTypeid extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
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
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array('TablesCreateMShop');
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array('MShopAddTypeData');
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Add column typeid to mshop_*_list tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Move list types to separate table', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true
				&& $this->_schema->columnExists( $table, 'type' ) === true
				&& $this->_schema->columnExists( $table, 'typeid' ) === false )
			{
				$this->_executeList( $stmtList );
				$this->_status( 'migrated' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}