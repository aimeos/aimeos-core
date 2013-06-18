<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_text_list_type"( "siteid", "code", "domain", "label", "status",
				"mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_text_list_type"
			SET "siteid"=?, "code" = ?, "domain" = ?, "label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_text_list_type"
			WHERE :cond
			AND siteid = ?
		',
		'search' => '
			SELECT mtexlity."id", mtexlity."siteid", mtexlity."code", mtexlity."domain",
				mtexlity."label", mtexlity."status", mtexlity."mtime", mtexlity."editor", mtexlity."ctime"
			FROM "mshop_text_list_type" AS mtexlity
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mtexlity."id"
				FROM "mshop_text_list_type" as mtexlity
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
