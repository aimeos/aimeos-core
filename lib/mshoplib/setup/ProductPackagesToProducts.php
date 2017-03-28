<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Moves product packages to sub-products.
 */
class ProductPackagesToProducts extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_product_package' => array(
			'ALTER TABLE "mshop_product" ADD "parentid" INTEGER DEFAULT NULL',
			'ALTER TABLE "mshop_product" ADD "packid" INTEGER DEFAULT NULL',
			'ALTER TABLE "mshop_product" ADD "pos" INTEGER DEFAULT NULL',
			'INSERT INTO "mshop_product"("parentid", "packid", "siteid", "code", "suppliercode", "label", "start", "end", "status", "pos" )
				SELECT mpp."prodid", mpp."id", mpp."siteid", mpp."code", mp."suppliercode", mpp."label", mpp."start", mpp."end", mpp."status", mpp."pos"
				FROM "mshop_product_package" AS mpp
				JOIN "mshop_product" AS mp ON ( mpp."prodid" = mp."id" )
			',
			'INSERT INTO "mshop_product_list"( "parentid", "siteid", "domain", "refid", "pos" )
				SELECT mp."parentid", mp."siteid", \'product\', mp."id", mp."pos"
				FROM "mshop_product" AS mp
				WHERE mp."packid" IS NOT NULL
			',
			'ALTER TABLE "mshop_product" DROP "pos", DROP "parentid"',
		),
		'mshop_product_package_text' => array(
			'INSERT INTO "mshop_product_list" ( "parentid", "siteid", "domain", "refid", "pos" )
				SELECT mp."id", mppt."siteid", \'text\', mppt."textid", mppt."pos"
				FROM "mshop_product_package_text" AS mppt
				JOIN "mshop_product" AS mp ON ( mppt."prodpackid" = mp."packid")
			',
			'DROP TABLE "mshop_product_package_text"',
		),
		'mshop_product_package_price' => array(
			'ALTER TABLE "mshop_price" ADD "packid" INTEGER DEFAULT NULL',
			'INSERT INTO "mshop_price" ( "siteid", "currencyid", "quantity", "price", "shipping", "discount", "taxrate", "status", "packid" )
				SELECT mppp."siteid", mppp."currencyid", mppp."quantity", mppp."price", mppp."shipping", mppp."discount", mppp."taxrate", mppp."status", mppp."prodpackid"
				FROM "mshop_product_package_price" AS mppp
			',
			'INSERT INTO "mshop_product_list"( "parentid", "siteid", "domain", "refid" )
				SELECT p."id", mp."siteid", \'price\', mp."id"
				FROM "mshop_price" AS mp
				JOIN "mshop_product" AS p ON ( p."packid" = mp."packid" )
				WHERE mp."packid" IS NOT NULL
			',
			'ALTER TABLE "mshop_price" DROP "packid"',
			'DROP TABLE "mshop_product_package_price"',
		),
		'mshop_product_package_stock' => array(
			'INSERT INTO "mshop_product_stock" ( "prodid", "siteid", "stocklevel", "backdate" )
				SELECT mp."id", mpps."siteid", mpps."stock", mpps."backdate"
				FROM "mshop_product_package_stock" AS mpps
				JOIN "mshop_product" AS mp ON ( mp."packid" = mpps."prodpackid" AND mp."packid" IS NOT NULL )
			',
			'DROP TABLE "mshop_product_package_stock"',
			'ALTER TABLE "mshop_product" DROP "packid"',
			'DROP TABLE "mshop_product_package"',
			'ALTER TABLE "mshop_order_base_product" DROP "packcode"',
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
		$this->msg( 'Migrating product text and media tables to list', 0 ); $this->status( '' );

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
