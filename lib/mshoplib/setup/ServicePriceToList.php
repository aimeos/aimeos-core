<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Moves service price references to list table.
 */
class ServicePriceToList extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_service_price' => array(
			'ALTER TABLE "mshop_price" ADD "serviceid" INTEGER DEFAULT NULL',
			'INSERT INTO "mshop_price" ( "siteid", "currencyid", "quantity", "price", "shipping", "discount", "taxrate", "status", "serviceid" )
				SELECT msp."siteid", msp."currencyid", 1, msp."price", msp."shipping", msp."discount", msp."taxrate", 1, msp."serviceid"
				FROM "mshop_service_price" as msp
			',
			'INSERT INTO "mshop_service_list" ( "parentid", "siteid", "domain", "refid" )
				SELECT mp."serviceid", mp."siteid", \'price\', mp."id"
				FROM "mshop_price" as mp
				WHERE "serviceid" IS NOT NULL
			',
			'ALTER TABLE "mshop_price" DROP "serviceid"',
			'DROP TABLE "mshop_service_price"',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop' );
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
		$this->process( $this->mysql );
	}


	/**
	 * Migrate product text and media data into the mshop_product_list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Migrating service price table to list', 0 ); $this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true )
			{
				$this->executeList( $stmtList );
				$this->status( 'migrated' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
