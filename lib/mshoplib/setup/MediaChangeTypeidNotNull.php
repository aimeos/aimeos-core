<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: MediaChangeTypeidNotNull.php 14251 2011-12-09 13:36:27Z nsendetzky $
 */


/**
 * Changes typeid column in media table to allow no NULL values any more.
 */
class MW_Setup_Task_MediaChangeTypeidNotNull extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'UPDATE "mshop_media" SET "typeid" = ( SELECT type."id" FROM "mshop_media_type" type WHERE type."code" = \'default\' AND type."domain" = \'attribute\' ) WHERE "domain" = \'attribute\' AND "typeid" IS NULL',
		'UPDATE "mshop_media" SET "typeid" = ( SELECT type."id" FROM "mshop_media_type" type WHERE type."code" = \'default\' AND type."domain" = \'catalog\' ) WHERE "domain" = \'catalog\' AND "typeid" IS NULL',
		'UPDATE "mshop_media" SET "typeid" = ( SELECT type."id" FROM "mshop_media_type" type WHERE type."code" = \'default\' AND type."domain" = \'media\' ) WHERE "domain" = \'media\' AND "typeid" IS NULL',
		'UPDATE "mshop_media" SET "typeid" = ( SELECT type."id" FROM "mshop_media_type" type WHERE type."code" = \'default\' AND type."domain" = \'product\' ) WHERE "domain" = \'product\' AND "typeid" IS NULL',
		'UPDATE "mshop_media" SET "typeid" = ( SELECT type."id" FROM "mshop_media_type" type WHERE type."code" = \'default\' AND type."domain" = \'service\' ) WHERE "domain" = \'service\' AND "typeid" IS NULL',
		'UPDATE "mshop_media" SET "typeid" = ( SELECT type."id" FROM "mshop_media_type" type WHERE type."code" = \'default\' AND type."domain" = \'text\' ) WHERE "domain" = \'text\' AND "typeid" IS NULL',
		'typeid' => 'ALTER TABLE "mshop_media" CHANGE "typeid" "typeid" INTEGER NOT NULL',
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
	 * Migrates service text data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Changing typeid of mshop_media table', 0 );

		if( $this->_schema->tableExists( 'mshop_media' ) === true
			&& $this->_schema->columnExists( 'mshop_media', 'typeid' ) === true
			&& $this->_schema->getColumnDetails( 'mshop_media', 'typeid' )->isNullable() === true )
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
