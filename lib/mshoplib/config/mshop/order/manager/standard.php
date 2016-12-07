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
				SELECT :key AS "key", mord."id" AS "id"
				FROM "mshop_order" AS mord
				:joins
				WHERE :cond
				GROUP BY :key, mord."id" /*-columns*/ , :columns /*columns-*/
				/*-orderby*/ ORDER BY :order /*orderby-*/
				LIMIT :size OFFSET :start
			) AS list
			GROUP BY "key"
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_order" (
				"baseid", "siteid", "type", "datepayment", "datedelivery",
				"statusdelivery", "statuspayment", "relatedid", "mtime",
				"editor", "ctime", "cdate", "cmonth", "cweek", "chour"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_order"
			SET "baseid" = ?, "siteid" = ?, "type" = ?, "datepayment" = ?,
				"datedelivery" = ?, "statusdelivery" = ?, "statuspayment" = ?,
				"relatedid" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_order"
			WHERE :cond AND siteid = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mord."id" AS "order.id", mord."baseid" AS "order.baseid",
				mord."siteid" AS "order.siteid", mord."type" AS "order.type",
				mord."datepayment" AS "order.datepayment", mord."datedelivery" AS "order.datedelivery",
				mord."statuspayment" AS "order.statuspayment", mord."statusdelivery" AS "order.statusdelivery",
				mord."relatedid" AS "order.relatedid", mord."ctime" AS "order.ctime",
				mord."mtime" AS "order.mtime", mord."editor" AS "order.editor"
			FROM "mshop_order" AS mord
			:joins
			WHERE :cond
			GROUP BY mord."id", mord."baseid", mord."siteid", mord."type",
				mord."datepayment", mord."datedelivery", mord."statuspayment", mord."statusdelivery",
				mord."relatedid", mord."ctime", mord."mtime", mord."editor"
				/*-columns*/ , :columns /*columns-*/
			/*-orderby*/ORDER BY :order/*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT( DISTINCT mord."id" ) AS "count"
			FROM "mshop_order" AS mord
			:joins
			WHERE :cond
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_order_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);
