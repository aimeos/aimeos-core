<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_media"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_media" (
				"siteid", "langid", "typeid", "label", "mimetype", "link",
				"status", "domain", "preview", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_media"
			SET "siteid" = ?, "langid" = ?, "typeid" = ?, "label" = ?,
				"mimetype" = ?, "link" = ?, "status" = ?, "domain" = ?,
				"preview" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mmed."id", mmed."siteid", mmed."langid",
				mmed."typeid", mmed."link" AS "url", mmed."label",
				mmed."status", mmed."mimetype", mmed."domain", mmed."preview",
				mmed."mtime", mmed."editor", mmed."ctime"
			FROM "mshop_media" AS mmed
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
				SELECT DISTINCT mmed."id"
				FROM "mshop_media" AS mmed
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

