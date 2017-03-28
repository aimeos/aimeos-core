<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes length of domain columns.
 */
class ColumnDomainChangeLength extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_attribute' => array(
			'ALTER TABLE "mshop_attribute" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_attribute" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_attribute_type' => array(
			'ALTER TABLE "mshop_attribute_type" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_attribute_type" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_attribute_list_type' => array(
			'ALTER TABLE "mshop_attribute_list_type" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_attribute_list_type" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_attribute_list' => array(
			'ALTER TABLE "mshop_attribute_list" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_attribute_list" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_catalog' => array(
			'ALTER TABLE "mshop_catalog" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_catalog" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_catalog_list_type' => array(
			'ALTER TABLE "mshop_catalog_list_type" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_catalog_list_type" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_catalog_list' => array(
			'ALTER TABLE "mshop_catalog_list" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_catalog_list" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_catalog_suggest' => array(
			'ALTER TABLE "mshop_catalog_suggest" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_catalog_suggest" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_media_type' => array(
			'ALTER TABLE "mshop_media_type" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_media_type" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_media_list_type' => array(
			'ALTER TABLE "mshop_media_list_type" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_media_list_type" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_media' => array(
			'ALTER TABLE "mshop_media" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_media" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_media_list' => array(
			'ALTER TABLE "mshop_media_list" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_media_list" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_plugin' => array(
			'ALTER TABLE "mshop_plugin" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_plugin" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_price' => array(
			'ALTER TABLE "mshop_price" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_price" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_product_type' => array(
			'ALTER TABLE "mshop_product_type" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_product_type" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_product_list_type' => array(
			'ALTER TABLE "mshop_product_list_type" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_product_list_type" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
			'UPDATE "mshop_product_list_type" SET "domain"=\'product/tag\' WHERE "domain"=\'prodtag\'',
		),
		'mshop_product_list' => array(
			'ALTER TABLE "mshop_product_list" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_product_list" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
			'UPDATE "mshop_product_list" SET "domain"=\'product/tag\' WHERE "domain"=\'prodtag\'',
		),
		'mshop_product_tag_type' => array(
			'ALTER TABLE "mshop_product_tag_type" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_product_tag_type" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_service' => array(
			'ALTER TABLE "mshop_service" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_service" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_service_list_type' => array(
			'ALTER TABLE "mshop_service_list_type" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_service_list_type" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_service_list' => array(
			'ALTER TABLE "mshop_service_list" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_service_list" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_text_type' => array(
			'ALTER TABLE "mshop_text_type" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_text_type" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_text' => array(
			'ALTER TABLE "mshop_text" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_text" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_text_list_type' => array(
			'ALTER TABLE "mshop_text_list_type" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_text_list_type" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
		),
		'mshop_text_list' => array(
			'ALTER TABLE "mshop_text_list" CHANGE "domain" "domain" VARCHAR(32) NOT NULL',
			'UPDATE "mshop_text_list" SET "domain"=\'attribute\' WHERE "domain"=\'attr\'',
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
	 * Migrates service text data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$column = 'domain';
		$this->msg( 'Changing "domain" columns', 0 ); $this->status( '' );

		foreach( $stmts as $table=>$stmtList )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true
				&& $this->schema->columnExists( $table, $column ) === true
				&& $this->schema->getColumnDetails( $table, $column )->getDataType() == "varchar"
				&& $this->schema->getColumnDetails( $table, $column )->getMaxLength() != 32 )
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
