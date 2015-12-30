<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_media_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_media_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_media_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mmedty."id" AS "media.type.id", mmedty."siteid" AS "media.type.siteid",
				mmedty."code" AS "media.type.code", mmedty."domain" AS "media.type.domain",
				mmedty."label" AS "media.type.label", mmedty."status" AS "media.type.status",
				mmedty."mtime" AS "media.type.mtime", mmedty."editor" AS "media.type.editor",
				mmedty."ctime" AS "media.type.ctime"
			FROM "mshop_media_type" mmedty
			:joins
			WHERE :cond
			GROUP BY mmedty."id", mmedty."siteid", mmedty."code", mmedty."domain",
				mmedty."label", mmedty."status", mmedty."mtime", mmedty."editor",
				mmedty."ctime" /*-orderby*/, :order /*orderby-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM(
				SELECT DISTINCT mmedty."id"
				FROM "mshop_media_type" mmedty
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

