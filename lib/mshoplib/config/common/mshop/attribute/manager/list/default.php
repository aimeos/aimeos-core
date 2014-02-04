<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'aggregate' => '
			SELECT :key AS "key", COUNT(DISTINCT mattli."id") AS "count"
			FROM "mshop_attribute_list" mattli
			:joins
			WHERE :cond
			GROUP BY :key
		',
		'getposmax' => '
			SELECT MAX( "pos" ) AS pos
			FROM "mshop_attribute_list"
			WHERE "siteid" = ?
				AND "parentid" = ?
				AND "typeid" = ?
				AND "domain" = ?
		',
		'insert' => '
			INSERT INTO "mshop_attribute_list"( "parentid", "siteid", "typeid", "domain", "refid", "start",
				"end", "config", "pos", "status", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_attribute_list"
			SET "parentid" = ?, "siteid" = ?, "typeid" = ?, "domain" = ?, "refid" = ?, "start" = ?, "end" = ?,
			"config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'updatepos' => '
			UPDATE "mshop_attribute_list"
				SET "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_attribute_list"
			WHERE :cond
			AND siteid = ?
		',
		'move' => '
			UPDATE "mshop_attribute_list"
				SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ?
				AND "parentid" = ?
				AND "typeid" = ?
				AND "domain" = ?
				AND "pos" >= ?
		',
		'search' => '
			SELECT mattli."id", mattli."parentid", mattli."siteid", mattli."typeid", mattli."domain",
				mattli."refid", mattli."start", mattli."end", mattli."config", mattli."pos", mattli."status", mattli."mtime", mattli."ctime", mattli."editor"
			FROM "mshop_attribute_list" AS mattli
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mattli."id"
				FROM "mshop_attribute_list" AS mattli
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
