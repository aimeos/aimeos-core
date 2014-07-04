<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_attribute_type"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_attribute_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		',
		'update' => '
			UPDATE "mshop_attribute_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mattty."id", mattty."siteid", mattty."code",
				mattty."domain", mattty."label", mattty."status",
				mattty."mtime", mattty."ctime", mattty."editor"
			FROM "mshop_attribute_type" AS mattty
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mattty."id"
				FROM "mshop_attribute_type" AS mattty
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
