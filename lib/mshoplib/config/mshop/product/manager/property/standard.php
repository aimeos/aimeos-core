<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_product_property"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_product_property" (
				"parentid", "siteid", "typeid", "langid", "value", "mtime", "editor",
				"ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_product_property"
			SET "parentid" = ?, "siteid" = ?, "typeid" = ?, "langid" = ?, "value" = ?,
				"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mpropr."id" AS "product.property.id", mpropr."parentid" AS "product.property.parentid",
				mpropr."siteid" AS "product.property.siteid", mpropr."typeid" AS "product.property.typeid",
				mpropr."langid" AS "product.property.languageid", mpropr."value" AS "product.property.value",
				mpropr."mtime" AS "product.property.mtime", mpropr."editor" AS "product.property.editor",
				mpropr."ctime" AS "product.property.ctime"
			FROM "mshop_product_property" AS mpropr
			:joins
			WHERE :cond
			GROUP BY mpropr."id", mpropr."parentid", mpropr."siteid", mpropr."typeid",
				mpropr."langid", mpropr."value", mpropr."mtime", mpropr."editor",
				mpropr."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mpropr."id"
				FROM "mshop_product_property" AS mpropr
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_product_property_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

