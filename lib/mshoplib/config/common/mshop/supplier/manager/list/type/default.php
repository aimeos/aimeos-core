<?php

/**
 * @copyright Aimeos (aimeos.org), 2015
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_supplier_list_type"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_supplier_list_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		',
		'update' => '
			UPDATE "mshop_supplier_list_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT msuplity."id", msuplity."siteid", msuplity."code",
				msuplity."domain", msuplity."label", msuplity."status",
				msuplity."mtime", msuplity."editor", msuplity."ctime"
			FROM "mshop_supplier_list_type" AS msuplity
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT msuplity."id"
				FROM "mshop_supplier_list_type" AS msuplity
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
