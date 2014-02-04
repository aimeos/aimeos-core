<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'aggregate' => '
			SELECT :key AS "key", COUNT(DISTINCT mmedli."id") AS "count"
			FROM "mshop_media_list" mmedli
			:joins
			WHERE :cond
			GROUP BY :key
		',
		'getposmax' => '
			SELECT MAX( "pos" ) AS pos
			FROM "mshop_media_list"
			WHERE "siteid" = ?
				AND "parentid" = ?
				AND "typeid" = ?
				AND "domain" = ?
		',
		'insert' => '
			INSERT INTO "mshop_media_list"( "parentid", "siteid", "typeid", "domain", "refid", "start",
				"end", "config", "pos", "status", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_media_list"
			SET "parentid"=?, "siteid" = ?, "typeid" = ?, "domain" = ?, "refid" = ?, "start" = ?, "end" = ?, "config" = ?,
				"pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'updatepos' => '
			UPDATE "mshop_media_list"
				SET "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_media_list"
			WHERE :cond
			AND siteid = ?
		',
		'move' => '
			UPDATE "mshop_media_list"
				SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ?
				AND "parentid" = ?
				AND "typeid" = ?
				AND "domain" = ?
				AND "pos" >= ?
		',
		'search' => '
			SELECT mmedli."id", mmedli."parentid", mmedli."siteid", mmedli."typeid",
				mmedli."domain", mmedli."refid", mmedli."start", mmedli."end", mmedli."config",
				mmedli."pos", mmedli."status", mmedli."mtime", mmedli."editor", mmedli."ctime"
			FROM "mshop_media_list" AS mmedli
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM(
				SELECT DISTINCT mmedli."id"
				FROM "mshop_media_list" AS mmedli
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
