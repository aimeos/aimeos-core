<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
			SELECT DISTINCT mpri."id", mpri."siteid", mpri."typeid",
				mpri."currencyid", mpri."domain", mpri."label",
				mpri."quantity", mpri."value", mpri."costs", mpri."rebate",
				mpri."taxrate", mpri."status", mpri."mtime", mpri."editor",
				mpri."ctime"
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
