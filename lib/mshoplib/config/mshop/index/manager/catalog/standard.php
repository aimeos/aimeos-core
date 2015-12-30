<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_index_catalog"
			WHERE :cond AND "siteid" = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_index_catalog" (
				"prodid", "siteid", "catid", "listtype", "pos", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mpro."id"
			FROM "mshop_product" AS mpro
			:joins
			WHERE :cond
			GROUP BY mpro."id" /*-orderby*/, :order /*orderby-*/
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
		DELETE FROM "mshop_index_catalog"
		WHERE "ctime" < ? AND "siteid" = ?
	'
	),
	'newid' => array(
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
	'optimize' => array(
		'mysql' => array(
			'OPTIMIZE TABLE "mshop_index_catalog"',
		),
	),
);
