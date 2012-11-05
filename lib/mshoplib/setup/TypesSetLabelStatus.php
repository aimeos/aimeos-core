<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: TypesSetLabelStatus.php 14251 2011-12-09 13:36:27Z nsendetzky $
 */


/**
 * Adds label and status values if not set.
 */
class MW_Setup_Task_TypesSetLabelStatus extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_attribute_type' => array(
			'UPDATE "mshop_attribute_type" SET "status" = 1 WHERE "code" IN (\'color\', \'size\', \'width\', \'length\') AND "label" = \'\' AND "status" = 0',
			'UPDATE "mshop_attribute_type" SET "label" = \'Color\' WHERE "code" = \'color\' AND "label" = \'\'',
			'UPDATE "mshop_attribute_type" SET "label" = \'Size\' WHERE "code" = \'size\' AND "label" = \'\'',
			'UPDATE "mshop_attribute_type" SET "label" = \'Width\' WHERE "code" = \'width\' AND "label" = \'\'',
			'UPDATE "mshop_attribute_type" SET "label" = \'Length\' WHERE "code" = \'length\' AND "label" = \'\'',
			'UPDATE "mshop_attribute_type" SET "label" = "code" WHERE "label" = \'\'',
		),
		'mshop_attribute_list_type' => array(
			'UPDATE "mshop_attribute_list_type" SET "status" = 1 WHERE "code" = \'default\' AND "label" = \'\' AND "status" = 0',
			'UPDATE "mshop_attribute_list_type" SET "label" = \'Default\' WHERE "code" = \'default\' AND "label" = \'\'',
			'UPDATE "mshop_attribute_list_type" SET "label" = "code" WHERE "label" = \'\'',
		),
		'mshop_catalog_list_type' => array(
			'UPDATE "mshop_catalog_list_type" SET "status" = 1 WHERE "code" = \'default\' AND "label" = \'\' AND "status" = 0',
			'UPDATE "mshop_catalog_list_type" SET "label" = \'Default\' WHERE "code" = \'default\' AND "label" = \'\'',
			'UPDATE "mshop_catalog_list_type" SET "status" = 1 WHERE "code" = \'promotion\' AND "label" = \'\' AND "status" = 0',
			'UPDATE "mshop_catalog_list_type" SET "label" = \'Promotion\' WHERE "code" = \'promotion\' AND "label" = \'\'',
			'UPDATE "mshop_catalog_list_type" SET "label" = "code" WHERE "label" = \'\'',
		),
		'mshop_media_list_type' => array(
			'UPDATE "mshop_media_list_type" SET "status" = 1 WHERE "code" = \'default\' AND "label" = \'\' AND "status" = 0',
			'UPDATE "mshop_media_list_type" SET "label" = \'Default\' WHERE "code" = \'default\' AND "label" = \'\'',
			'UPDATE "mshop_media_list_type" SET "label" = "code" WHERE "label" = \'\'',
		),
		'mshop_media_type' => array(
			'UPDATE "mshop_media_type" SET "status" = 1 WHERE "code" = \'default\' AND "label" = \'\' AND "status" = 0',
			'UPDATE "mshop_media_type" SET "label" = \'Default\' WHERE "code" = \'default\' AND "label" = \'\'',
			'UPDATE "mshop_media_type" SET "label" = "code" WHERE "label" = \'\'',
		),
		'mshop_plugin_type' => array(
			'UPDATE "mshop_plugin_type" SET "status" = 1 WHERE "code" = \'order\' AND "label" = \'\' AND "status" = 0',
			'UPDATE "mshop_plugin_type" SET "label" = \'Order\' WHERE "code" = \'order\' AND "label" = \'\'',
			'UPDATE "mshop_plugin_type" SET "label" = "code" WHERE "label" = \'\'',
		),
		'mshop_product_list_type' => array(
			'UPDATE "mshop_product_list_type" SET "status" = 1 WHERE "code" = \'default\' AND "label" = \'\' AND "status" = 0',
			'UPDATE "mshop_product_list_type" SET "label" = \'Default\' WHERE "code" = \'default\' AND "label" = \'\'',
			'UPDATE "mshop_product_list_type" SET "status" = 1 WHERE "code" = \'suggestion\' AND "label" = \'\' AND "status" = 0',
			'UPDATE "mshop_product_list_type" SET "label" = \'Suggestion\' WHERE "code" = \'suggestion\' AND "label" = \'\'',
			'UPDATE "mshop_product_list_type" SET "label" = "code" WHERE "label" = \'\'',
		),
		'mshop_product_tag_type' => array(
			'UPDATE "mshop_product_tag_type" SET "status" = 1 WHERE "code" = \'default\' AND "label" = \'\' AND "status" = 0',
			'UPDATE "mshop_product_tag_type" SET "label" = \'Default\' WHERE "code" = \'default\' AND "label" = \'\'',
			'UPDATE "mshop_product_tag_type" SET "label" = "code" WHERE "label" = \'\'',
		),
		'mshop_product_type' => array(
			'UPDATE "mshop_product_type" SET "status" = 1 WHERE "code" IN (\'product\', \'select\', \'bundle\') AND "label" = \'\' AND "status" = 0',
			'UPDATE "mshop_product_type" SET "label" = \'Product\' WHERE "code" = \'product\' AND "label" = \'\'',
			'UPDATE "mshop_product_type" SET "label" = \'Selection\' WHERE "code" = \'select\' AND "label" = \'\'',
			'UPDATE "mshop_product_type" SET "label" = \'Bundle\' WHERE "code" = \'bundle\' AND "label" = \'\'',
			'UPDATE "mshop_product_type" SET "label" = "code" WHERE "label" = \'\'',
		),
		'mshop_service_list_type' => array(
			'UPDATE "mshop_service_list_type" SET "status" = 1 WHERE "code" = \'default\' AND "label" = \'\' AND "status" = 0',
			'UPDATE "mshop_service_list_type" SET "label" = \'Default\' WHERE "code" = \'default\' AND "label" = \'\'',
			'UPDATE "mshop_service_list_type" SET "label" = "code" WHERE "label" = \'\'',
		),
		'mshop_service_type' => array(
			'UPDATE "mshop_service_type" SET "status" = 1 WHERE "code" IN (\'delivery\', \'payment\') AND "label" = \'\' AND "status" = 0',
			'UPDATE "mshop_service_type" SET "label" = \'Delivery\' WHERE "code" = \'delivery\' AND "label" = \'\'',
			'UPDATE "mshop_service_type" SET "label" = \'Payment\' WHERE "code" = \'payment\' AND "label" = \'\'',
			'UPDATE "mshop_service_type" SET "label" = "code" WHERE "label" = \'\'',
		),
		'mshop_text_list_type' => array(
			'UPDATE "mshop_text_list_type" SET "status" = 1 WHERE "code" = \'default\' AND "label" = \'\' AND "status" = 0',
			'UPDATE "mshop_text_list_type" SET "label" = \'Default\' WHERE "code" = \'default\' AND "label" = \'\'',
			'UPDATE "mshop_text_list_type" SET "label" = "code" WHERE "label" = \'\'',
		),
		'mshop_text_type' => array(
			'UPDATE "mshop_text_type" SET "status" = 1 WHERE "code" IN (\'name\', \'short\', \'long\') AND "label" = \'\' AND "status" = 0',
			'UPDATE "mshop_text_type" SET "label" = \'Name\' WHERE "code" = \'name\' AND "label" = \'\'',
			'UPDATE "mshop_text_type" SET "label" = \'Short description\' WHERE "code" = \'short\' AND "label" = \'\'',
			'UPDATE "mshop_text_type" SET "label" = \'Long description\' WHERE "code" = \'long\' AND "label" = \'\'',
			'UPDATE "mshop_text_type" SET "label" = "code" WHERE "label" = \'\'',
		)
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array('TypesAddLabelStatus');
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
		$this->_msg(sprintf('Setting label and status values'), 0);
		$this->_status( '' );

		foreach ($this->_mysql as $table => $stmts)
		{
			$this->_msg(sprintf('Checking table "%1$s": ', $table), 1);

			if ($this->_schema->tableExists($table)) {
				$this->_executeList($stmts);
				$this->_status('OK');
			} else {
				$this->_status('n/a');
			}
		}
	}
}