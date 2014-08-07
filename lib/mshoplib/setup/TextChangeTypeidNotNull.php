<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Changes type ID columns to NOT NULL for text table.
 */
class MW_Setup_Task_TextChangeTypeidNotNull extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'UPDATE "mshop_text" SET "typeid" = ( SELECT type."id" FROM "mshop_text_type" type WHERE type."code" = \'default\' AND type."domain" = \'attribute\' ) WHERE "domain" = \'attribute\' AND "typeid" IS NULL',
		'UPDATE "mshop_text" SET "typeid" = ( SELECT type."id" FROM "mshop_text_type" type WHERE type."code" = \'default\' AND type."domain" = \'catalog\' ) WHERE "domain" = \'catalog\' AND "typeid" IS NULL',
		'UPDATE "mshop_text" SET "typeid" = ( SELECT type."id" FROM "mshop_text_type" type WHERE type."code" = \'default\' AND type."domain" = \'media\' ) WHERE "domain" = \'media\' AND "typeid" IS NULL',
		'UPDATE "mshop_text" SET "typeid" = ( SELECT type."id" FROM "mshop_text_type" type WHERE type."code" = \'default\' AND type."domain" = \'product\' ) WHERE "domain" = \'product\' AND "typeid" IS NULL',
		'UPDATE "mshop_text" SET "typeid" = ( SELECT type."id" FROM "mshop_text_type" type WHERE type."code" = \'default\' AND type."domain" = \'service\' ) WHERE "domain" = \'service\' AND "typeid" IS NULL',
		'UPDATE "mshop_text" SET "typeid" = ( SELECT type."id" FROM "mshop_text_type" type WHERE type."code" = \'default\' AND type."domain" = \'text\' ) WHERE "domain" = \'text\' AND "typeid" IS NULL',
		'typeid' => 'ALTER TABLE "mshop_text" CHANGE "typeid" "typeid" INTEGER NOT NULL',
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
	 * Migrates service text data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Changing typeid of mshop_text table', 0 );

		if( $this->_schema->tableExists( 'mshop_text' ) === true
			&& $this->_schema->columnExists( 'mshop_text', 'typeid' ) === true
			&& $this->_schema->getColumnDetails( 'mshop_text', 'typeid' )->isNullable() === true )
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
