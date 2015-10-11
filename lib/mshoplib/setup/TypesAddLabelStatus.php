<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds label and status columns to all type tables.
 */
class TypesAddLabelStatus extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_attribute_type' => array(
			'label'  => 'ALTER TABLE "mshop_attribute_type" ADD "label" VARCHAR(255) NOT NULL',
			'status' => 'ALTER TABLE "mshop_attribute_type" ADD "status" smallint(6) NOT NULL DEFAULT 0 AFTER label'
		),
		'mshop_attribute_list_type' => array(
			'label'  => 'ALTER TABLE "mshop_attribute_list_type" ADD "label" VARCHAR(255) NOT NULL',
			'status' => 'ALTER TABLE "mshop_attribute_list_type" ADD "status" smallint(6) NOT NULL DEFAULT 0 AFTER label'
		),
		'mshop_catalog_list_type' => array(
			'label'  => 'ALTER TABLE "mshop_catalog_list_type" ADD "label" VARCHAR(255) NOT NULL',
			'status' => 'ALTER TABLE "mshop_catalog_list_type" ADD "status" smallint(6) NOT NULL DEFAULT 0 AFTER label'
		),
		'mshop_media_list_type' => array(
			'label'  => 'ALTER TABLE "mshop_media_list_type" ADD "label" VARCHAR(255) NOT NULL',
			'status' => 'ALTER TABLE "mshop_media_list_type" ADD "status" smallint(6) NOT NULL DEFAULT 0 AFTER label'
		),
		'mshop_media_type' => array(
			'label'  => 'ALTER TABLE "mshop_media_type" ADD "label" VARCHAR(255) NOT NULL',
			'status' => 'ALTER TABLE "mshop_media_type" ADD "status" smallint(6) NOT NULL DEFAULT 0 AFTER label'
		),
		'mshop_plugin_type' => array(
			'label'  => 'ALTER TABLE "mshop_plugin_type" ADD "label" VARCHAR(255) NOT NULL',
			'status' => 'ALTER TABLE "mshop_plugin_type" ADD "status" smallint(6) NOT NULL DEFAULT 0 AFTER label'
		),
		'mshop_product_list_type' => array(
			'label'  => 'ALTER TABLE "mshop_product_list_type" ADD "label" VARCHAR(255) NOT NULL',
			'status' => 'ALTER TABLE "mshop_product_list_type" ADD "status" smallint(6) NOT NULL DEFAULT 0 AFTER label'
		),
		'mshop_product_tag_type' => array(
			'label'  => 'ALTER TABLE "mshop_product_tag_type" ADD "label" VARCHAR(255) NOT NULL',
			'status' => 'ALTER TABLE "mshop_product_tag_type" ADD "status" smallint(6) NOT NULL DEFAULT 0 AFTER label'
		),
		'mshop_product_type' => array(
			'label'  => 'ALTER TABLE "mshop_product_type" ADD "label" VARCHAR(255) NOT NULL',
			'status' => 'ALTER TABLE "mshop_product_type" ADD "status" smallint(6) NOT NULL DEFAULT 0 AFTER label'
		),
		'mshop_service_list_type' => array(
			'label'  => 'ALTER TABLE "mshop_service_list_type" ADD "label" VARCHAR(255) NOT NULL',
			'status' => 'ALTER TABLE "mshop_service_list_type" ADD "status" smallint(6) NOT NULL DEFAULT 0 AFTER label'
		),
		'mshop_service_type' => array(
			'label'  => 'ALTER TABLE "mshop_service_type" ADD "label" VARCHAR(255) NOT NULL',
			'status' => 'ALTER TABLE "mshop_service_type" ADD "status" smallint(6) NOT NULL DEFAULT 0 AFTER label'
		),
		'mshop_text_list_type' => array(
			'label'  => 'ALTER TABLE "mshop_text_list_type" ADD "label" VARCHAR(255) NOT NULL',
			'status' => 'ALTER TABLE "mshop_text_list_type" ADD "status" smallint(6) NOT NULL DEFAULT 0 AFTER label'
		),
		'mshop_text_type' => array(
			'label'  => 'ALTER TABLE "mshop_text_type" ADD "label" VARCHAR(255) NOT NULL',
			'status' => 'ALTER TABLE "mshop_text_type" ADD "status" smallint(6) NOT NULL DEFAULT 0 AFTER label'
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
		$this->msg( sprintf( 'Adding label and status columns' ), 0 );
		$this->status( '' );

		foreach( $this->mysql as $table => $columns ) {

			if( $this->schema->tableExists( $table ) ) {

				foreach( $columns as $column => $stmt ) {

					$this->msg( sprintf( 'Checking column "%1$s.%2$s": ', $table, $column ), 1 );

					if( !$this->schema->columnExists( $table, $column ) ) {
						$this->execute( $stmt );
						$this->status( 'added' );
					} else {
						$this->status( 'OK' );
					}
				}
			}
		}
	}
}