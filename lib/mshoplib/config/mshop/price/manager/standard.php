<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_price"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_price" (
				"siteid", "typeid", "currencyid", "domain", "label",
				"quantity", "value", "costs", "rebate", "taxrate", "status",
				"mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_price"
			SET "siteid" = ?, "typeid" = ?, "currencyid" = ?, "domain" = ?,
				"label" = ?, "quantity" = ?, "value" = ?, "costs" = ?,
				"rebate" = ?, "taxrate" = ?, "status" = ?, "mtime" = ?,
				"editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mpri."id" AS "price.id", mpri."siteid" AS "price.siteid",
				mpri."typeid" AS "price.typeid", mpri."currencyid" AS "price.currencyid",
				mpri."domain" AS "price.domain", mpri."label" AS "price.label",
				mpri."quantity" AS "price.quantity", mpri."value" AS "price.value",
				mpri."costs" AS "price.costs", mpri."rebate" AS "price.rebate",
				mpri."taxrate" AS "price.taxrate", mpri."status" AS "price.status",
				mpri."mtime" AS "price.mtime", mpri."editor" AS "price.editor",
				mpri."ctime" AS "price.ctime"
			FROM "mshop_price" AS mpri
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mpri."id"
				FROM "mshop_price" AS mpri
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
);
