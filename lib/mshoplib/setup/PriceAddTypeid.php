<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds typeid column to price table.
 */
class PriceAddTypeid extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'ALTER TABLE "mshop_price" ADD "typeid" INTEGER NULL AFTER "siteid"',
		'UPDATE "mshop_price" SET "typeid" = ( SELECT mprity."id" FROM "mshop_price_type" mprity WHERE mprity."code" = \'default\' AND mprity."domain" = \'product\' AND mprity."siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) ) WHERE "domain" = \'product\'',
		'UPDATE "mshop_price" SET "typeid" = ( SELECT mprity."id" FROM "mshop_price_type" mprity WHERE mprity."code" = \'default\' AND mprity."domain" = \'service\' AND mprity."siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) ) WHERE "domain" = \'service\'',
		'UPDATE "mshop_price" SET "typeid" = ( SELECT mprity."id" FROM "mshop_price_type" mprity WHERE mprity."code" = \'default\' AND mprity."domain" = \'attribute\' AND mprity."siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' ) ) WHERE "domain" = \'attribute\'',
		'ALTER TABLE "mshop_price" MODIFY "typeid" INTEGER NOT NULL',
		'ALTER TABLE "mshop_price" ADD CONSTRAINT "fk_mspri_typeid" FOREIGN KEY ( "typeid" ) REFERENCES "mshop_price_type" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop', 'MShopAddLocaleData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->msg( 'Adding typeid column to price table', 0 ); $this->status( '' );

		$this->process( $this->mysql );
	}


	/**
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( sprintf( 'Checking column "%1$s": ', 'typeid' ), 1 );

		if( $this->schema->tableExists( 'mshop_price' ) === true
			&& $this->schema->columnExists( 'mshop_price', 'typeid' ) === false )
		{
			$this->executeList( $stmts );
			$this->status( 'added' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}