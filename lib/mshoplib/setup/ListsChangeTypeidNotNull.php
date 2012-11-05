<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: ListsChangeTypeidNotNull.php 14251 2011-12-09 13:36:27Z nsendetzky $
 */


/**
 * Changes typeid columns in list tables to allow no NULL values any more.
 */
class MW_Setup_Task_ListsChangeTypeidNotNull
	extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_attribute_list' => array(
			'UPDATE "mshop_attribute_list" SET "typeid" = ( SELECT type."id" FROM "mshop_attribute_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'attribute\' ) WHERE "domain" = \'attribute\' AND "typeid" IS NULL',
			'UPDATE "mshop_attribute_list" SET "typeid" = ( SELECT type."id" FROM "mshop_attribute_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'catalog\' ) WHERE "domain" = \'catalog\' AND "typeid" IS NULL',
			'UPDATE "mshop_attribute_list" SET "typeid" = ( SELECT type."id" FROM "mshop_attribute_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'media\' ) WHERE "domain" = \'media\' AND "typeid" IS NULL',
			'UPDATE "mshop_attribute_list" SET "typeid" = ( SELECT type."id" FROM "mshop_attribute_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'product\' ) WHERE "domain" = \'product\' AND "typeid" IS NULL',
			'UPDATE "mshop_attribute_list" SET "typeid" = ( SELECT type."id" FROM "mshop_attribute_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'service\' ) WHERE "domain" = \'service\' AND "typeid" IS NULL',
			'UPDATE "mshop_attribute_list" SET "typeid" = ( SELECT type."id" FROM "mshop_attribute_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'text\' ) WHERE "domain" = \'text\' AND "typeid" IS NULL',
			'ALTER TABLE "mshop_attribute_list" CHANGE "typeid" "typeid" INTEGER NOT NULL',
		),
		'mshop_text_list' => array(
			'UPDATE "mshop_text_list" SET "typeid" = ( SELECT type."id" FROM "mshop_text_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'attribute\' ) WHERE "domain" = \'attribute\' AND "typeid" IS NULL',
			'UPDATE "mshop_text_list" SET "typeid" = ( SELECT type."id" FROM "mshop_text_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'catalog\' ) WHERE "domain" = \'catalog\' AND "typeid" IS NULL',
			'UPDATE "mshop_text_list" SET "typeid" = ( SELECT type."id" FROM "mshop_text_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'media\' ) WHERE "domain" = \'media\' AND "typeid" IS NULL',
			'UPDATE "mshop_text_list" SET "typeid" = ( SELECT type."id" FROM "mshop_text_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'product\' ) WHERE "domain" = \'product\' AND "typeid" IS NULL',
			'UPDATE "mshop_text_list" SET "typeid" = ( SELECT type."id" FROM "mshop_text_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'service\' ) WHERE "domain" = \'service\' AND "typeid" IS NULL',
			'UPDATE "mshop_text_list" SET "typeid" = ( SELECT type."id" FROM "mshop_text_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'text\' ) WHERE "domain" = \'text\' AND "typeid" IS NULL',
			'ALTER TABLE "mshop_text_list" CHANGE "typeid" "typeid" INTEGER NOT NULL',
		),
		'mshop_catalog_list' => array(
			'UPDATE "mshop_catalog_list" SET "typeid" = ( SELECT type."id" FROM "mshop_catalog_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'attribute\' ) WHERE "domain" = \'attribute\' AND "typeid" IS NULL',
			'UPDATE "mshop_catalog_list" SET "typeid" = ( SELECT type."id" FROM "mshop_catalog_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'catalog\' ) WHERE "domain" = \'catalog\' AND "typeid" IS NULL',
			'UPDATE "mshop_catalog_list" SET "typeid" = ( SELECT type."id" FROM "mshop_catalog_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'media\' ) WHERE "domain" = \'media\' AND "typeid" IS NULL',
			'UPDATE "mshop_catalog_list" SET "typeid" = ( SELECT type."id" FROM "mshop_catalog_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'product\' ) WHERE "domain" = \'product\' AND "typeid" IS NULL',
			'UPDATE "mshop_catalog_list" SET "typeid" = ( SELECT type."id" FROM "mshop_catalog_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'service\' ) WHERE "domain" = \'service\' AND "typeid" IS NULL',
			'UPDATE "mshop_catalog_list" SET "typeid" = ( SELECT type."id" FROM "mshop_catalog_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'text\' ) WHERE "domain" = \'text\' AND "typeid" IS NULL',
			'ALTER TABLE "mshop_catalog_list" CHANGE "typeid" "typeid" INTEGER NOT NULL',
		),
		'mshop_product_list' => array(
			'UPDATE "mshop_product_list" SET "typeid" = ( SELECT type."id" FROM "mshop_product_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'attribute\' ) WHERE "domain" = \'attribute\' AND "typeid" IS NULL',
			'UPDATE "mshop_product_list" SET "typeid" = ( SELECT type."id" FROM "mshop_product_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'catalog\' ) WHERE "domain" = \'catalog\' AND "typeid" IS NULL',
			'UPDATE "mshop_product_list" SET "typeid" = ( SELECT type."id" FROM "mshop_product_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'media\' ) WHERE "domain" = \'media\' AND "typeid" IS NULL',
			'UPDATE "mshop_product_list" SET "typeid" = ( SELECT type."id" FROM "mshop_product_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'product\' ) WHERE "domain" = \'product\' AND "typeid" IS NULL',
			'UPDATE "mshop_product_list" SET "typeid" = ( SELECT type."id" FROM "mshop_product_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'service\' ) WHERE "domain" = \'service\' AND "typeid" IS NULL',
			'UPDATE "mshop_product_list" SET "typeid" = ( SELECT type."id" FROM "mshop_product_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'text\' ) WHERE "domain" = \'text\' AND "typeid" IS NULL',
			'ALTER TABLE "mshop_product_list" CHANGE "typeid" "typeid" INTEGER NOT NULL',
		),
		'mshop_service_list' => array(
			'UPDATE "mshop_service_list" SET "typeid" = ( SELECT type."id" FROM "mshop_service_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'attribute\' ) WHERE "domain" = \'attribute\' AND "typeid" IS NULL',
			'UPDATE "mshop_service_list" SET "typeid" = ( SELECT type."id" FROM "mshop_service_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'catalog\' ) WHERE "domain" = \'catalog\' AND "typeid" IS NULL',
			'UPDATE "mshop_service_list" SET "typeid" = ( SELECT type."id" FROM "mshop_service_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'media\' ) WHERE "domain" = \'media\' AND "typeid" IS NULL',
			'UPDATE "mshop_service_list" SET "typeid" = ( SELECT type."id" FROM "mshop_service_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'product\' ) WHERE "domain" = \'product\' AND "typeid" IS NULL',
			'UPDATE "mshop_service_list" SET "typeid" = ( SELECT type."id" FROM "mshop_service_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'service\' ) WHERE "domain" = \'service\' AND "typeid" IS NULL',
			'UPDATE "mshop_service_list" SET "typeid" = ( SELECT type."id" FROM "mshop_service_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'text\' ) WHERE "domain" = \'text\' AND "typeid" IS NULL',
			'ALTER TABLE "mshop_service_list" CHANGE "typeid" "typeid" INTEGER NOT NULL',
		),
		'mshop_media_list' => array(
			'UPDATE "mshop_media_list" SET "typeid" = ( SELECT type."id" FROM "mshop_media_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'attribute\' ) WHERE "domain" = \'attribute\' AND "typeid" IS NULL',
			'UPDATE "mshop_media_list" SET "typeid" = ( SELECT type."id" FROM "mshop_media_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'catalog\' ) WHERE "domain" = \'catalog\' AND "typeid" IS NULL',
			'UPDATE "mshop_media_list" SET "typeid" = ( SELECT type."id" FROM "mshop_media_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'media\' ) WHERE "domain" = \'media\' AND "typeid" IS NULL',
			'UPDATE "mshop_media_list" SET "typeid" = ( SELECT type."id" FROM "mshop_media_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'product\' ) WHERE "domain" = \'product\' AND "typeid" IS NULL',
			'UPDATE "mshop_media_list" SET "typeid" = ( SELECT type."id" FROM "mshop_media_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'service\' ) WHERE "domain" = \'service\' AND "typeid" IS NULL',
			'UPDATE "mshop_media_list" SET "typeid" = ( SELECT type."id" FROM "mshop_media_list_type" type WHERE type."code" = \'default\' AND type."domain" = \'text\' ) WHERE "domain" = \'text\' AND "typeid" IS NULL',
			'ALTER TABLE "mshop_media_list" CHANGE "typeid" "typeid" INTEGER NOT NULL',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ListAddTypeid' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array();
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_msg( 'Changing typeid to not allow NULL values', 0 );
		$this->_status( '' );

		foreach ( $this->_mysql as $table => $stmt ) {
			$this->_msg( sprintf ( 'Checking table "%1$s": ', $table ), 1 );

			if ( $this->_schema->tableExists( $table ) === true
				&& $this->_schema->columnExists( $table, 'typeid' ) === true
				&& $this->_schema->getColumnDetails( $table, 'typeid' )->isNullable() === true )
			{
				$this->_executeList( $stmt );
				$this->_status( 'migrated' );
			} else {
				$this->_status( 'OK' );
			}
		}
	}
}