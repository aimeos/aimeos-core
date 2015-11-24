<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_customer_list_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_customer_list_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_customer_list_type"
			SET "siteid"=?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mcuslity."id" AS "customer.lists.type.id", mcuslity."siteid" AS "customer.lists.type.siteid",
				mcuslity."code" AS "customer.lists.type.code", mcuslity."domain" AS "customer.lists.type.domain",
				mcuslity."label" AS "customer.lists.type.label", mcuslity."status" AS "customer.lists.type.status",
				mcuslity."mtime" AS "customer.lists.type.mtime", mcuslity."editor" AS "customer.lists.type.editor",
				mcuslity."ctime" AS "customer.lists.type.ctime"
			FROM "mshop_customer_list_type" AS mcuslity
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcuslity."id"
				FROM "mshop_customer_list_type" as mcuslity
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS LIST
		'
	),
	'newid' => array(
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
);

