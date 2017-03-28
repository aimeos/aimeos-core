<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes typeid column in media table to allow no NULL values any more.
 */
class MediaChangeTypeidNotNull extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
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
		$this->msg( 'Changing typeid of mshop_media table', 0 );

		if( $this->schema->tableExists( 'mshop_media' ) === true
			&& $this->schema->columnExists( 'mshop_media', 'typeid' ) === true
			&& $this->schema->getColumnDetails( 'mshop_media', 'typeid' )->isNullable() === true )
		{
			$this->executeList( $stmts );
			$this->status( 'migrated' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
