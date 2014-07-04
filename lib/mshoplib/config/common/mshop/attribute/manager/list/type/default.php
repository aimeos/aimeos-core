<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_attribute_list_type"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_attribute_list_type"(
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		',
		'update' => '
			UPDATE "mshop_attribute_list_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mattlity."id", mattlity."siteid", mattlity."code",
				mattlity."domain", mattlity."label", mattlity."status",
				mattlity."mtime", mattlity."ctime", mattlity."editor"
			FROM "mshop_attribute_list_type" AS mattlity
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mattlity."id"
				FROM "mshop_attribute_list_type" AS mattlity
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
