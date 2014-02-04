<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'aggregate' => '
			SELECT :key AS "key", COUNT(DISTINCT mtexli."id") AS "count"
			FROM "mshop_text_list" mtexli
			:joins
			WHERE :cond
			GROUP BY :key
		',
		'getposmax' => '
			SELECT MAX( "pos" ) AS pos
			FROM "mshop_text_list"
			WHERE "siteid" = ?
				AND "parentid" = ?
				AND "typeid" = ?
				AND "domain" = ?
		',
		'insert' => '
			INSERT INTO "mshop_text_list"( "parentid", "siteid", "typeid", "domain", "refid", "start", "end", "config",
				"pos", "status", "mtime", "editor", "ctime")
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_text_list"
			SET "parentid"=?, "siteid" = ?, "typeid" = ?, "domain" = ?, "refid" = ?, "start" = ?, "end" = ?, "config" = ?,
				"pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'updatepos' => '
			UPDATE "mshop_text_list"
				SET "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_text_list"
			WHERE :cond
			AND siteid = ?
		',
		'move' => '
			UPDATE "mshop_text_list"
				SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ?
				AND "parentid" = ?
				AND "typeid" = ?
				AND "domain" = ?
				AND "pos" >= ?
		',
		'search' => '
			SELECT mtexli."id", mtexli."parentid", mtexli."siteid", mtexli."typeid",
				mtexli."domain", mtexli."refid", mtexli."start", mtexli."end", mtexli."config",
				mtexli."pos", mtexli."status", mtexli."mtime", mtexli."editor", mtexli."ctime"
			FROM "mshop_text_list" AS mtexli
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mtexli."id"
				FROM "mshop_text_list" AS mtexli
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
