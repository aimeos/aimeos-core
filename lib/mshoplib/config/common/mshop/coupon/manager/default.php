<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_coupon"
			WHERE :cond
			AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_coupon" ("siteid", "label", "provider", "config",
				"start", "end", "status", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_coupon"
			SET "siteid" = ?, "label" = ?, "provider" = ?, "config" = ?, "start" = ?,
				"end" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mcou."id", mcou."siteid", mcou."label", mcou."provider", mcou."config", mcou."status", mcou."start",
				mcou."end", mcou."mtime", mcou."editor", mcou."ctime"
			FROM "mshop_coupon" AS mcou
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(DISTINCT  mcou."id") AS "count"
			FROM "mshop_coupon" AS mcou
			:joins
			WHERE :cond
		',
	),
);
