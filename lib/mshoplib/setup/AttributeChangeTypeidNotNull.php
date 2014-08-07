<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */



/**
 * Changes typeid column in attribute table to allow no NULL values any more.
 */
class MW_Setup_Task_AttributeChangeTypeidNotNull extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'UPDATE "mshop_attribute" SET "typeid" = ( SELECT type."id" FROM "mshop_attribute_type" type WHERE type."code" = \'default\' AND type."domain" = \'attribute\' ) WHERE "domain" = \'attribute\'',
		'UPDATE "mshop_attribute" SET "typeid" = ( SELECT type."id" FROM "mshop_attribute_type" type WHERE type."code" = \'default\' AND type."domain" = \'catalog\' ) WHERE "domain" = \'catalog\'',
		'UPDATE "mshop_attribute" SET "typeid" = ( SELECT type."id" FROM "mshop_attribute_type" type WHERE type."code" = \'default\' AND type."domain" = \'media\' ) WHERE "domain" = \'media\'',
		'UPDATE "mshop_attribute" SET "typeid" = ( SELECT type."id" FROM "mshop_attribute_type" type WHERE type."code" = \'default\' AND type."domain" = \'product\' ) WHERE "domain" = \'product\'',
		'UPDATE "mshop_attribute" SET "typeid" = ( SELECT type."id" FROM "mshop_attribute_type" type WHERE type."code" = \'default\' AND type."domain" = \'service\' ) WHERE "domain" = \'service\'',
		'UPDATE "mshop_attribute" SET "typeid" = ( SELECT type."id" FROM "mshop_attribute_type" type WHERE type."code" = \'default\' AND type."domain" = \'text\' ) WHERE "domain" = \'text\'',
		'ALTER TABLE "mshop_attribute" CHANGE "typeid" "typeid" INTEGER NOT NULL',
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
		return array('TablesCreateMShop', 'MShopAddTypeData');
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Migrates service text data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Changing typeid of mshop_attribute table', 0 );

		if( $this->_schema->tableExists( 'mshop_attribute' ) === true
			&& $this->_schema->columnExists( 'mshop_attribute', 'typeid' ) === true
			&& $this->_schema->getColumnDetails( 'mshop_attribute', 'typeid' )->isNullable() === true )
		{
			$this->_executeList( $stmts );
			$this->_status( 'migrated' );
		}
		else
		{
			$this->_status( 'OK' );
		}
	}
}
