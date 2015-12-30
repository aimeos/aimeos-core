<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_media_list_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_media_list_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_media_list_type"
			SET "siteid"=?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mmedlity."id" AS "media.lists.type.id", mmedlity."siteid" AS "media.lists.type.siteid",
				mmedlity."code" AS "media.lists.type.code", mmedlity."domain" AS "media.lists.type.domain",
				mmedlity."label" AS "media.lists.type.label", mmedlity."status" AS "media.lists.type.status",
				mmedlity."mtime" AS "media.lists.type.mtime", mmedlity."editor" AS "media.lists.type.editor",
				mmedlity."ctime" AS "media.lists.type.ctime"
			FROM "mshop_media_list_type" AS mmedlity
			:joins
			WHERE :cond
			GROUP BY mmedlity."id", mmedlity."siteid", mmedlity."code", mmedlity."domain",
				mmedlity."label", mmedlity."status", mmedlity."mtime", mmedlity."editor",
				mmedlity."ctime" /*-orderby*/, :order /*orderby-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM(
				SELECT DISTINCT mmedlity."id"
				FROM "mshop_media_list_type" AS mmedlity
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

