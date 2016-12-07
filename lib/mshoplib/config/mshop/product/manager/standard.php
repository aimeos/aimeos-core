<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_product"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_product" (
				"siteid", "typeid", "code", "label", "status",
				"start", "end", "config", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_product"
			SET "siteid" = ?, "typeid" = ?, "code" = ?, "label" = ?, "status" = ?,
				"start" = ?, "end" = ?, "config" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mpro."id" AS "product.id", mpro."siteid" AS "product.siteid",
				mpro."typeid" AS "product.typeid", mpro."code" AS "product.code",
				mpro."label" AS "product.label", mpro."config" AS "product.config",
				mpro."start" AS "product.datestart", mpro."end" AS "product.dateend",
				mpro."status" AS "product.status", mpro."ctime" AS "product.ctime",
				mpro."mtime" AS "product.mtime", mpro."editor" AS "product.editor"
			FROM "mshop_product" AS mpro
			:joins
			WHERE :cond
			GROUP BY mpro."id", mpro."siteid", mpro."typeid", mpro."code",
				mpro."label", mpro."config", mpro."start", mpro."end",
				mpro."status", mpro."ctime", mpro."mtime", mpro."editor"
				/*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mpro."id"
				FROM "mshop_product" AS mpro
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_product_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

