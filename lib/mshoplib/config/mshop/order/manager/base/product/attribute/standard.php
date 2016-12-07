<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'aggregate' => array(
		'ansi' => '
		SELECT "key", COUNT("id") AS "count"
		FROM (
			SELECT :key AS "key", mordbaprat."id" AS "id"
			FROM "mshop_order_base_product_attr" AS mordbaprat
			:joins
			WHERE :cond
			GROUP BY :key, mordbaprat."id" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		) AS list
		GROUP BY "key"
	'
	),
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_order_base_product_attr"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_order_base_product_attr" (
				"siteid", "attrid", "ordprodid", "type", "code", "value",
				"name", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_order_base_product_attr"
			SET "siteid" = ?, "attrid" = ?, "ordprodid" = ?, "type" = ?,
				"code" = ?, "value" = ?, "name" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mordbaprat."id" AS "order.base.product.attribute.id", mordbaprat."siteid" AS "order.base.product.attribute.siteid",
				mordbaprat."attrid" AS "order.base.product.attribute.attributeid", mordbaprat."ordprodid" AS "order.base.product.attribute.parentid",
				mordbaprat."type" AS "order.base.product.attribute.type", mordbaprat."code" AS "order.base.product.attribute.code",
				mordbaprat."value" AS "order.base.product.attribute.value", mordbaprat."name" AS "order.base.product.attribute.name",
				mordbaprat."mtime" AS "order.base.product.attribute.mtime", mordbaprat."editor" AS "order.base.product.attribute.editor",
				mordbaprat."ctime" AS "order.base.product.attribute.ctime"
			FROM "mshop_order_base_product_attr" AS mordbaprat
			:joins
			WHERE :cond
			GROUP BY mordbaprat."id", mordbaprat."siteid", mordbaprat."attrid", mordbaprat."ordprodid",
				mordbaprat."type", mordbaprat."code", mordbaprat."value", mordbaprat."name",
				mordbaprat."mtime", mordbaprat."editor", mordbaprat."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT( DISTINCT mordbaprat."id" ) AS "count"
			FROM "mshop_order_base_product_attr" AS mordbaprat
			:joins
			WHERE :cond
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_order_base_product_attr_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

