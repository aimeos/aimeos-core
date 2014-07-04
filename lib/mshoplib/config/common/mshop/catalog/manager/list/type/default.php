<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_catalog_list_type"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_catalog_list_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		',
		'update' => '
			UPDATE "mshop_catalog_list_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mcatlity."id", mcatlity."siteid", mcatlity."code",
				mcatlity."domain", mcatlity."label", mcatlity."mtime",
				mcatlity."editor", mcatlity."ctime", mcatlity."status"
			FROM "mshop_catalog_list_type" AS mcatlity
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcatlity."id"
				FROM "mshop_catalog_list_type" AS mcatlity
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
