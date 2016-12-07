<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
			SELECT mpri."id" AS "price.id", mpri."siteid" AS "price.siteid",
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
			GROUP BY mpri."id", mpri."siteid", mpri."typeid", mpri."currencyid",
				mpri."domain", mpri."label", mpri."quantity", mpri."value",
				mpri."costs", mpri."rebate", mpri."taxrate", mpri."status",
				mpri."mtime", mpri."editor", mpri."ctime" /*-columns*/ , :columns /*columns-*/
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
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_price_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);
