<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_service"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_service" (
				"siteid", "pos", "typeid", "code", "label", "provider",
				"config", "status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		',
		'update' => '
			UPDATE "mshop_service"
			SET "siteid" = ?, "pos" = ?, "typeid" = ?, "code" = ?, "label" = ?,
				"provider" = ?, "config" = ?, "status" = ?, "mtime" = ?,
				"editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mser."id", mser."siteid", mser."pos",
				mser."typeid", mser."code", mser."label", mser."provider",
				mser."config", mser."status", mser."mtime", mser."editor",
				mser."ctime"
			FROM "mshop_service" AS mser
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT count(*) as "count"
			FROM (
				SELECT DISTINCT mser."id"
				FROM "mshop_service" AS mser
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
