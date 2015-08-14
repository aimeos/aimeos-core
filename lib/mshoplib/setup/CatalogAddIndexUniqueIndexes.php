<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds unique indexes to all catalog index tables.
 */
class MW_Setup_Task_CatalogAddIndexUniqueIndexes extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_catalog_index_attribute' => array(
			'unq_mscatinat_p_s_aid_lt' => array(
				'CREATE TEMPORARY TABLE "tmp_mshop_catalog_index_attribute"
					SELECT "prodid", "siteid", "attrid", "listtype", "type", "code", "ctime", "mtime", "editor"
					FROM "mshop_catalog_index_attribute"
					GROUP BY "prodid", "siteid", "attrid", "listtype", "type", "code"
					HAVING COUNT(*) > 1',
				'DELETE t1 FROM "mshop_catalog_index_attribute" AS t1, "tmp_mshop_catalog_index_attribute" AS t2 WHERE
					t1."prodid" = t2."prodid" AND t1."siteid" = t2."siteid" AND t1."attrid" = t2."attrid" AND
					t1."listtype" = t2."listtype" AND t1."type" = t2."type" AND  t1."code" = t2."code"',
				'INSERT INTO "mshop_catalog_index_attribute"
					("prodid", "siteid", "attrid", "listtype", "type", "code", "ctime", "mtime", "editor")
					SELECT "prodid", "siteid", "attrid", "listtype", "type", "code", "ctime", "mtime", "editor"
					FROM "tmp_mshop_catalog_index_attribute"',
				'ALTER IGNORE TABLE "mshop_catalog_index_attribute"
					ADD UNIQUE INDEX "unq_mscatinat_p_s_aid_lt" ("prodid", "siteid", "attrid", "listtype")',
				'DROP INDEX "idx_mscatinat_p_s_at_lt" ON "mshop_catalog_index_attribute"',
			),
		),
		'mshop_catalog_index_catalog' => array(
			'unq_mscatinca_p_s_cid_lt_po' => array(
				'CREATE TEMPORARY TABLE "tmp_mshop_catalog_index_catalog"
					SELECT "prodid", "siteid", "catid", "listtype", "pos", "ctime", "mtime", "editor"
					FROM "mshop_catalog_index_catalog"
					GROUP BY "prodid", "siteid", "catid", "listtype"
					HAVING COUNT(*) > 1',
				'DELETE t1 FROM "mshop_catalog_index_catalog" AS t1, "tmp_mshop_catalog_index_catalog" AS t2 WHERE
					t1."prodid" = t2."prodid" AND t1."siteid" = t2."siteid" AND t1."catid" = t2."catid" AND
					t1."listtype" = t2."listtype" AND t1."pos" = t2."pos"',
				'INSERT INTO "mshop_catalog_index_catalog"
					("prodid", "siteid", "catid", "listtype", "pos", "ctime", "mtime", "editor")
					SELECT "prodid", "siteid", "catid", "listtype", "pos", "ctime", "mtime", "editor"
					FROM "tmp_mshop_catalog_index_catalog"',
				'ALTER IGNORE TABLE "mshop_catalog_index_catalog"
					ADD UNIQUE INDEX "unq_mscatinca_p_s_cid_lt_po" ("prodid", "siteid", "catid", "listtype", "pos")',
				'DROP INDEX "idx_mscatinca_p_s_ca_lt_po" ON "mshop_catalog_index_catalog"',
			),
		),
		'mshop_catalog_index_price' => array(
			'unq_mscatinpr_p_s_prid_lt' => array(
				'CREATE TEMPORARY TABLE "tmp_mshop_catalog_index_price"
					SELECT "prodid", "siteid", "priceid", "listtype", "type", "currencyid",
						"value", "shipping", "rebate", "taxrate", "quantity", "ctime", "mtime", "editor"
					FROM "mshop_catalog_index_price"
					GROUP BY "prodid", "siteid", "priceid", "listtype", "type", "currencyid",
						"value", "shipping", "rebate", "taxrate", "quantity"
					HAVING COUNT(*) > 1',
				'DELETE t1 FROM "mshop_catalog_index_price" AS t1, "tmp_mshop_catalog_index_price" AS t2 WHERE
					t1."prodid" = t2."prodid" AND t1."siteid" = t2."siteid" AND t1."priceid" = t2."priceid" AND
					t1."listtype" = t2."listtype" AND t1."type" = t2."type" AND t1."currencyid" = t2."currencyid" AND
					t1."value" = t2."value" AND t1."shipping" = t2."shipping" AND t1."rebate" = t2."rebate" AND
					t1."taxrate" = t2."taxrate" AND t1."quantity" = t2."quantity"',
				'INSERT INTO "mshop_catalog_index_price"
					("prodid", "siteid", "priceid", "listtype", "type", "currencyid",
						"value", "shipping", "rebate", "taxrate", "quantity", "ctime", "mtime", "editor")
					SELECT "prodid", "siteid", "priceid", "listtype", "type", "currencyid",
						"value", "shipping", "rebate", "taxrate", "quantity", "ctime", "mtime", "editor"
					FROM "tmp_mshop_catalog_index_price"',
				'ALTER IGNORE TABLE "mshop_catalog_index_price"
					ADD UNIQUE INDEX "unq_mscatinpr_p_s_prid_lt" ("prodid", "siteid", "priceid", "listtype")',
			),
		),
		'mshop_catalog_index_text' => array(
			'unq_mscatinte_p_s_tid_lt' => array(
				'CREATE TEMPORARY TABLE "tmp_mshop_catalog_index_text"
					SELECT "prodid", "siteid", "textid", "langid", "listtype", "type", "domain", "value", "ctime", "mtime", "editor"
					FROM "mshop_catalog_index_text"
					GROUP BY "prodid", "siteid", "textid", "langid", "listtype", "type", "domain", "value"
					HAVING COUNT(*) > 1',
				'DELETE t1 FROM "mshop_catalog_index_text" AS t1, "tmp_mshop_catalog_index_text" AS t2 WHERE
					t1."prodid" = t2."prodid" AND t1."siteid" = t2."siteid" AND t1."textid" = t2."textid" AND
					t1."langid" = t2."langid" AND t1."listtype" = t2."listtype" AND t1."type" = t2."type" AND
					t1."domain" = t2."domain" AND t1."value" = t2."value"',
				'INSERT INTO "mshop_catalog_index_text"
					("prodid", "siteid", "textid", "langid", "listtype", "type", "domain", "value", "ctime", "mtime", "editor")
					SELECT "prodid", "siteid", "textid", "langid", "listtype", "type", "domain", "value", "ctime", "mtime", "editor"
					FROM "tmp_mshop_catalog_index_text"',
				'ALTER IGNORE TABLE "mshop_catalog_index_text"
					ADD UNIQUE INDEX "unq_mscatinte_p_s_tid_lt" ("prodid", "siteid", "textid", "listtype")',
			),
		),
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogAddIndexPriceidTextid' );
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
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}

	/**
	 * Adds unique constraints to catalog index tables.
	 *
	 * @param array $stmts Associative list of table name / constraint name / SQL statement to execute
	 */
	protected function _process( $stmts )
	{
		$this->_msg( 'Adding unique constraints to catalog index tables', 0 );
		$this->_status( '' );

		foreach( $stmts as $table => $list )
		{
			foreach( $list as $index => $sqls )
			{
				$this->_msg( sprintf( 'Checking index "%1$s"', $index ), 1 );

				if( $this->_schema->tableExists( $table ) === true
					&& $this->_schema->constraintExists( $table, $index ) === false )
				{
					$this->_executeList( $sqls );
					$this->_status( 'added' );
				}
				else
				{
					$this->_status( 'OK' );
				}
			}
		}
	}
}