<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_customer_group"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_customer_group" (
				"siteid", "code", "label", "mtime", "editor", "ctime"
			) VALUES (
				?,?,?,?,?,?
			)
		',
		'update' => '
			UPDATE "mshop_customer_group"
			SET "siteid" = ?, "code" = ?, "label" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mcusgr."id", mcusgr."siteid", mcusgr."code", mcusgr."label",
				mcusgr."mtime", mcusgr."editor", mcusgr."ctime"
			FROM "mshop_customer_group" AS mcusgr
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcusgr."id"
				FROM "mshop_customer_group" AS mcusgr
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);