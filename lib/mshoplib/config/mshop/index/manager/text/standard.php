<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_index_text"
			WHERE :cond AND "siteid" = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_index_text" (
				"prodid", "siteid", "textid", "langid", "listtype", "type",
				"domain", "value", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		',
		'pgsql' => '
			INSERT INTO "mshop_index_text" (
				"prodid", "siteid", "textid", "langid", "listtype", "type",
				"domain", "value", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
			ON CONFLICT DO NOTHING
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mpro."id"
			FROM "mshop_product" AS mpro
			:joins
			WHERE :cond
			GROUP BY mpro."id" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mpro."id"
				FROM "mshop_product" AS mpro
				:joins
				WHERE :cond
				LIMIT 1000 OFFSET 0
			) AS list
		'
	),
	'cleanup' => array(
		'ansi' => '
			DELETE FROM "mshop_index_text"
			WHERE "ctime" < ? AND "siteid" = ?
		'
	),
	'text' => array(
		'ansi' => '
			SELECT DISTINCT mindte."prodid", mindte."value"
			FROM "mshop_index_text" AS mindte
			JOIN "mshop_product" AS mpro ON mpro."id" = mindte."prodid"
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
	),
	'optimize' => array(
		'mysql' => array(
			'OPTIMIZE TABLE "mshop_index_text"',
		),
		'pgsql' => [],
	),
);
