<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_plugin_type"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_plugin_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		',
		'update' => '
			UPDATE "mshop_plugin_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mpluty."id", mpluty."siteid", mpluty."code",
				mpluty."domain", mpluty."label", mpluty."status",
				mpluty."mtime", mpluty."editor", mpluty."ctime"
			FROM "mshop_plugin_type" mpluty
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mpluty."id"
				FROM "mshop_plugin_type" mpluty
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	)
);
