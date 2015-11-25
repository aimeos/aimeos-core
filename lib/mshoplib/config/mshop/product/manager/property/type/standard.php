<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_product_property_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_product_property_type" (
				"siteid", "code", "domain", "label", "status",
				"mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_product_property_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mproprty."id" AS "product.property.type.id", mproprty."siteid" AS "product.property.type.siteid",
				mproprty."code" AS "product.property.type.code", mproprty."domain" AS "product.property.type.domain",
				mproprty."label" AS "product.property.type.label", mproprty."status" AS "product.property.type.status",
				mproprty."mtime" AS "product.property.type.mtime", mproprty."editor" AS "product.property.type.editor",
				mproprty."ctime" AS "product.property.type.ctime"
			FROM "mshop_product_property_type" mproprty
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
				SELECT DISTINCT mproprty."id"
				FROM "mshop_product_property_type" mproprty
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

