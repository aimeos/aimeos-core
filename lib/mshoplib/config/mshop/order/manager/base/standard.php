<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'aggregate' => array(
		'ansi' => '
		SELECT "key", COUNT("id") AS "count"
		FROM (
			SELECT DISTINCT :key AS "key", mordba."id" AS "id"
			FROM "mshop_order_base" AS mordba
			:joins
			WHERE :cond
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
				"price", "costs", "rebate", "comment", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_order_base"
			SET "siteid" = ?, "customerid" = ?, "sitecode" = ?, "langid" = ?,
				"currencyid" = ?, "price" = ?, "costs" = ?, "rebate" = ?,
				"comment" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mordba."id" AS "order.base.id", mordba."siteid" AS "order.base.siteid",
				mordba."sitecode" AS "order.base.sitecode", mordba."customerid" AS "order.base.customerid",
				mordba."langid" AS "order.base.languageid", mordba."currencyid" AS "order.base.currencyid",
				mordba."price" AS "order.base.price", mordba."costs" AS "order.base.costs",
				mordba."rebate" AS "order.base.rebate", mordba."comment" AS "order.base.comment",
				mordba."status" AS "order.base.status", mordba."mtime" AS "order.base.mtime",
				mordba."editor" AS "order.base.editor", mordba."ctime" AS "order.base.ctime"
			FROM "mshop_order_base" AS mordba
			:joins
			WHERE :cond
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
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
);

