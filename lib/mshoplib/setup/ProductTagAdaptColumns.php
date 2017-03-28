<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Moves domain to separate type table and references tags via product list table.
 */
class ProductTagAdaptColumns extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_product_tag' => array(
			'typeid' => array(
				// add tag type
				'ALTER TABLE "mshop_product_tag" ADD "typeid" INT DEFAULT NULL AFTER "siteid"',
				'ALTER TABLE "mshop_product_tag" ADD CONSTRAINT "fk_msprota_typeid" FOREIGN KEY ("typeid") REFERENCES "mshop_product_tag_type" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
				'CREATE TABLE "mshop_product_tag_type_tmp" SELECT * FROM "mshop_product_tag" WHERE "siteid" IS NULL GROUP BY "domain"',
				'INSERT INTO "mshop_product_tag_type" ("siteid", "code", "domain") SELECT "siteid", "domain", \'prodtag\' FROM "mshop_product_tag_type_tmp"',
				'INSERT INTO "mshop_product_tag_type" ("siteid", "code", "domain") SELECT "siteid", "domain", \'prodtag\' FROM "mshop_product_tag" WHERE "domain" NOT IN(SELECT "domain" FROM "mshop_product_tag_type_tmp") GROUP BY "domain", "siteid"',
				'UPDATE "mshop_product_tag" AS pt, "mshop_product_tag_type" AS ptt SET pt."typeid"=ptt."id" WHERE pt."domain"=ptt."code" AND pt."siteid"=ptt."siteid"',
				'UPDATE "mshop_product_tag" AS pt, "mshop_product_tag_type" AS ptt SET pt."typeid"=ptt."id" WHERE pt."domain"=ptt."code" AND pt."typeid" IS NULL',
				'DROP TABLE "mshop_product_tag_type_tmp"',
				'ALTER TABLE "mshop_product_tag" DROP "domain"'
			),
			'id' => array(
				'ALTER TABLE "mshop_product_tag" DROP FOREIGN KEY "fk_msprota_prodid"',
				'CREATE TABLE "mshop_product_tag_copy1" SELECT * FROM "mshop_product_tag"',
				'TRUNCATE TABLE "mshop_product_tag"',
				'ALTER TABLE "mshop_product_tag" DROP INDEX "unq_msprota_pid_sid_lid_label", ADD CONSTRAINT "unq_msprota_sid_tid_lid_label" UNIQUE ("siteid", "typeid", "langid", "label")',
				'ALTER TABLE "mshop_product_tag" ADD "id" INT( 11 ) UNSIGNED NOT NULL DEFAULT \'0\' FIRST',
				'ALTER TABLE "mshop_product_tag" ADD PRIMARY KEY ( "id" )',
				'ALTER TABLE "mshop_product_tag" CHANGE "id" "id" INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT',
				'INSERT INTO "mshop_product_tag"("prodid", "siteid", "langid", "label", "typeid") SELECT "prodid", "siteid", "langid", "label", "typeid" FROM "mshop_product_tag_copy1" WHERE "siteid" IS NULL GROUP BY "label", "typeid", "langid"',
				'CREATE TABLE "mshop_product_tag_copy2" SELECT * FROM "mshop_product_tag"',
				'INSERT INTO "mshop_product_tag"("prodid", "siteid", "langid", "label", "typeid") SELECT "prodid", "siteid", "langid", "label", "typeid" FROM "mshop_product_tag_copy1" WHERE "siteid" IS NOT NULL AND CONCAT("label",\':\',"typeid",\':\',"langid") NOT IN ( SELECT CONCAT("label",\':\',"typeid",\':\',"langid") FROM "mshop_product_tag_copy2" ) GROUP BY "label", "typeid", "langid"',
				'DROP TABLE "mshop_product_tag_copy2"',
				'ALTER TABLE "mshop_product_tag" DROP "prodid"',
				'ALTER TABLE "mshop_product_tag_copy1" ADD "tagid" INT DEFAULT 0',
				'UPDATE "mshop_product_tag_copy1" AS ptt, "mshop_product_tag" AS pt SET ptt."tagid"=pt."id" WHERE ptt."label"=pt."label" AND ptt."langid"=pt."langid" AND ptt."siteid"=pt."siteid" AND ptt."tagid"=0',
				'UPDATE "mshop_product_tag_copy1" AS ptt, "mshop_product_tag" AS pt SET ptt."tagid"=pt."id" WHERE ptt."label"=pt."label" AND ptt."langid"=pt."langid" AND pt."siteid" IS NULL AND ptt."tagid"=0',
				'INSERT INTO "mshop_product_list" ("parentid" ,"siteid" ,"domain" ,"refid") SELECT "prodid", "siteid", \'prodtag\', "tagid" FROM "mshop_product_tag_copy1" WHERE "tagid" > 0',
				'DROP TABLE "mshop_product_tag_copy1"',
			)
		)
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop', 'MShopAddTypeData' );
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
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{

		foreach( $stmts as $table=>$stmt )
		{
			if( $this->schema->tableExists( $table ) === true )
			{
				$this->msg( sprintf( 'Adding columns to table "%1$s"', $table ), 0 ); $this->status( '' );

				foreach( $stmt as $column=>$stmtList )
				{
					$this->msg( sprintf( 'Checking column "%1$s": ', $column ), 1 );

					if( $this->schema->columnExists( $table, $column ) === false )
					{
						$this->executeList( $stmtList );
						$this->status( 'added' );
					} else {
						$this->status( 'OK' );
					}
				}
			}
		}
	}
}