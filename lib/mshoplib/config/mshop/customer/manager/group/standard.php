<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_customer_group"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_customer_group" (
				"siteid", "code", "label", "mtime", "editor", "ctime"
			) VALUES (
				?,?,?,?,?,?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_customer_group"
			SET "siteid" = ?, "code" = ?, "label" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mcusgr."id" AS "customer.group.id", mcusgr."siteid" AS "customer.group.siteid",
				mcusgr."code" AS "customer.group.code", mcusgr."label" AS "customer.group.label",
				mcusgr."mtime" AS "customer.group.mtime", mcusgr."editor" AS "customer.group.editor",
				mcusgr."ctime" AS "customer.group.ctime"
			FROM "mshop_customer_group" AS mcusgr
			:joins
			WHERE :cond
			GROUP BY mcusgr."id", mcusgr."siteid", mcusgr."code", mcusgr."label",
				mcusgr."mtime", mcusgr."editor", mcusgr."ctime" /*-orderby*/, :order /*orderby-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcusgr."id"
				FROM "mshop_customer_group" AS mcusgr
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
);
