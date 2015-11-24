<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_product_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_product_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_product_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
			"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mproty."id" AS "product.type.id", mproty."siteid" AS "product.type.siteid",
				mproty."code" AS "product.type.code", mproty."domain" AS "product.type.domain",
				mproty."label" AS "product.type.label", mproty."status" AS "product.type.status",
				mproty."mtime" AS "product.type.mtime", mproty."editor" AS "product.type.editor",
				mproty."ctime" AS "product.type.ctime"
			FROM "mshop_product_type" AS mproty
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
				SELECT DISTINCT mproty."id"
				FROM "mshop_product_type" AS mproty
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

