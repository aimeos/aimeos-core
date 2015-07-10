<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds unique constraints to *_type tables.
 */
class MW_Setup_Task_TypesRenameConstraints extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_attribute_type' => array(
			'unq_msattty_sid_dom_code' => 'ALTER TABLE "mshop_attribute_type" DROP INDEX "unq_msattty_siteid_code", ADD CONSTRAINT "unq_msattty_sid_dom_code" UNIQUE ("siteid", "domain", "code")',
		),
		'mshop_media_type' => array(
			'unq_msmedty_sid_dom_code' => 'ALTER TABLE "mshop_media_type" DROP INDEX "unq_msmedty_siteid_code", ADD CONSTRAINT "unq_msmedty_sid_dom_code" UNIQUE ("siteid", "domain", "code")',
		),
		'mshop_text_type' => array(
			'unq_mstexty_sid_dom_code' => 'ALTER TABLE "mshop_text_type" DROP INDEX "unq_mstexty_siteid_code", ADD CONSTRAINT "unq_mstexty_sid_dom_code" UNIQUE ("siteid", "domain", "code")',
		),
		'mshop_attribute_list_type' => array(
			'unq_msattlity_sid_dom_code' => 'ALTER TABLE "mshop_attribute_list_type" ADD CONSTRAINT "unq_msattlity_sid_dom_code" UNIQUE ("siteid", "domain", "code")',
		),
		'mshop_catalog_list_type' => array(
			'unq_mscatlity_sid_dom_code' => 'ALTER TABLE "mshop_catalog_list_type" ADD CONSTRAINT "unq_mscatlity_sid_dom_code" UNIQUE ("siteid", "domain", "code")',
		),
		'mshop_media_list_type' => array(
			'unq_msmedlity_sid_dom_code' => 'ALTER TABLE "mshop_media_list_type" ADD CONSTRAINT "unq_msmedlity_sid_dom_code" UNIQUE ("siteid", "domain", "code")',
		),
		'mshop_product_type' => array(
			'unq_msproty_sid_dom_code' => 'ALTER TABLE "mshop_product_type" ADD CONSTRAINT "unq_msproty_sid_dom_code" UNIQUE ("siteid", "domain", "code")',
		),
		'mshop_product_list_type' => array(
			'unq_msprolity_sid_dom_code' => 'ALTER TABLE "mshop_product_list_type" ADD CONSTRAINT "unq_msprolity_sid_dom_code" UNIQUE ("siteid", "domain", "code")',
		),
		'mshop_product_tag_type' => array(
			'unq_msprotaty_sid_dom_code' => 'ALTER TABLE "mshop_product_tag_type" ADD CONSTRAINT "unq_msprotaty_sid_dom_code" UNIQUE ("siteid", "domain", "code")',
		),
		'mshop_service_list_type' => array(
			'unq_msserlity_sid_dom_code' => 'ALTER TABLE "mshop_service_list_type" ADD CONSTRAINT "unq_msserlity_sid_dom_code" UNIQUE ("siteid", "domain", "code")',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array('TypesAddDomain');
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array('TablesCreateMShop');
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Renaming type constraints', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			foreach ( $stmtList as $constraint=>$stmt )
			{
				$this->_msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

				if( $this->_schema->tableExists( $table ) && !$this->_schema->constraintExists( $table, $constraint ) )
				{
					$this->_execute( $stmt );
					$this->_status( 'changed' );
				} else {
					$this->_status( 'OK' );
				}
			}
		}
	}
}
