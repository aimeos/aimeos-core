<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_price_list_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_price_list_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_price_list_type"
			SET "siteid"=?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mprility."id" AS "price.lists.type.id", mprility."siteid" AS "price.lists.type.siteid",
				mprility."code" AS "price.lists.type.code", mprility."domain" AS "price.lists.type.domain",
				mprility."label" AS "price.lists.type.label", mprility."status" AS "price.lists.type.status",
				mprility."mtime" AS "price.lists.type.mtime", mprility."editor" AS "price.lists.type.editor",
				mprility."ctime" AS "price.lists.type.ctime"
			FROM "mshop_price_list_type" AS mprility
			:joins
			WHERE :cond
			GROUP BY mprility."id", mprility."siteid", mprility."code", mprility."domain",
				mprility."label", mprility."status", mprility."mtime", mprility."editor",
				mprility."ctime" /*-orderby*/, :order /*orderby-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mprility."id"
				FROM "mshop_price_list_type" AS mprility
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

