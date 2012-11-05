<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: TypesAddDomain.php 14251 2011-12-09 13:36:27Z nsendetzky $
 */


/**
 * Adds domain columns to all type tables.
 */
class MW_Setup_Task_TypesAddDomain extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_attribute_list_type' => 'ALTER TABLE "mshop_attribute_list_type" ADD "domain" VARCHAR(8) NOT NULL AFTER "siteid"',
		'mshop_attribute_option_list_type' => 'ALTER TABLE "mshop_attribute_option_list_type" ADD "domain" VARCHAR(8) NOT NULL AFTER "siteid"',
		'mshop_catalog_list_type' => 'ALTER TABLE "mshop_catalog_list_type" ADD "domain" VARCHAR(8) NOT NULL AFTER "siteid"',
		'mshop_product_list_type' => 'ALTER TABLE "mshop_product_list_type" ADD "domain" VARCHAR(8) NOT NULL AFTER "siteid"',
		'mshop_service_list_type' => 'ALTER TABLE "mshop_service_list_type" ADD "domain" VARCHAR(8) NOT NULL AFTER "siteid"',
		'mshop_media_type' => 'ALTER TABLE "mshop_media_type" ADD "domain" VARCHAR(8) NOT NULL AFTER "siteid"',
		'mshop_product_type' => 'ALTER TABLE "mshop_product_type" ADD "domain" VARCHAR(8) NOT NULL AFTER "siteid"',
		'mshop_text_type' => 'ALTER TABLE "mshop_text_type" ADD "domain" VARCHAR(8) NOT NULL AFTER "siteid"',
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
	 * @return array List of task names
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
		$this->_msg( 'Add column \'domain\' to mshop_*_type', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) && $this->_schema->columnExists( $table, 'domain' ) === false )
			{
				$this->_execute( $stmt );
				$this->_status( 'added' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}
