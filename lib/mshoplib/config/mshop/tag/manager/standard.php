<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_tag"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_tag" (
				"siteid", "langid", "typeid", "label", "mtime", "editor",
				"ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_tag"
			SET "siteid" = ?, "langid" = ?, "typeid" = ?, "label" = ?,
				"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mtag."id" AS "tag.id", mtag."siteid" AS "tag.siteid",
				mtag."typeid" AS "tag.typeid", mtag."langid" AS "tag.languageid",
				mtag."label" AS "tag.label", mtag."mtime" AS "tag.mtime",
				mtag."editor" AS "tag.editor", mtag."ctime" AS "tag.ctime"
			FROM "mshop_tag" AS mtag
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
				SELECT DISTINCT mtag."id"
				FROM "mshop_tag" AS mtag
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

