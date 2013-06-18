<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_service_list" ( "parentid", "siteid", "typeid", "domain", "refid", "start", "end",
				"pos", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_service_list"
			SET "parentid"=?, "siteid" = ?, "typeid" = ?, "domain" = ?, "refid" = ?, "start" = ?, "end" = ?,
				"pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_service_list"
			WHERE :cond
			AND siteid = ?
		',
		'move' => '
			UPDATE "mshop_service_list"
				SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ?
				AND "parentid" = ?
				AND "typeid" = ?
				AND "domain" = ?
				AND "pos" >= ?
		',
		'updatepos' => '
			UPDATE "mshop_service_list"
				SET "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'getposmax' => '
			SELECT MAX( "pos" ) AS pos
			FROM "mshop_service_list"
			WHERE "siteid" = ?
				AND "parentid" = ?
				AND "typeid" = ?
				AND "domain" = ?
		',
		'search' => '
			SELECT mserli."id", mserli."parentid", mserli."siteid", mserli."typeid",
				mserli."domain", mserli."refid", mserli."start", mserli."end", mserli."pos",
				mserli."mtime", mserli."editor", mserli."ctime"
			FROM "mshop_service_list" AS mserli
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mserli."id"
				FROM "mshop_service_list" AS mserli
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
