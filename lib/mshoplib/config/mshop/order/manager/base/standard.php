<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'aggregate' => array(
		'ansi' => '
		SELECT "key", COUNT("id") AS "count"
		FROM (
			SELECT :key AS "key", mordba."id" AS "id"
			FROM "mshop_order_base" AS mordba
			:joins
			WHERE :cond
			GROUP BY :key, mordba."id" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		) AS list
		GROUP BY "key"
	'
	),
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_order_base"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_order_base" (
				"siteid", "customerid", "sitecode", "langid", "currencyid",
				"price", "costs", "rebate", "tax", "taxflag", "comment", "status",
				"mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_order_base"
			SET "siteid" = ?, "customerid" = ?, "sitecode" = ?, "langid" = ?,
				"currencyid" = ?, "price" = ?, "costs" = ?, "rebate" = ?, "tax" = ?,
				"taxflag" = ?, "comment" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mordba."id" AS "order.base.id", mordba."siteid" AS "order.base.siteid",
				mordba."sitecode" AS "order.base.sitecode", mordba."customerid" AS "order.base.customerid",
				mordba."langid" AS "order.base.languageid", mordba."currencyid" AS "order.base.currencyid",
				mordba."price" AS "order.base.price", mordba."costs" AS "order.base.costs",
				mordba."rebate" AS "order.base.rebate", mordba."tax" AS "order.base.taxvalue",
				mordba."taxflag" AS "order.base.taxflag", mordba."comment" AS "order.base.comment",
				mordba."status" AS "order.base.status", mordba."mtime" AS "order.base.mtime",
				mordba."editor" AS "order.base.editor", mordba."ctime" AS "order.base.ctime"
			FROM "mshop_order_base" AS mordba
			:joins
			WHERE :cond
			GROUP BY mordba."id", mordba."siteid", mordba."sitecode", mordba."customerid",
				mordba."langid", mordba."currencyid", mordba."price", mordba."costs",
				mordba."rebate", mordba."tax", mordba."taxflag", mordba."comment", mordba."status",
				mordba."mtime", mordba."editor", mordba."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT( DISTINCT mordba."id" ) AS "count"
			FROM "mshop_order_base" AS mordba
			:joins
			WHERE :cond
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_order_base_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

