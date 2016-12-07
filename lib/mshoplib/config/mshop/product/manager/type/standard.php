<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
			SELECT mproty."id" AS "product.type.id", mproty."siteid" AS "product.type.siteid",
				mproty."code" AS "product.type.code", mproty."domain" AS "product.type.domain",
				mproty."label" AS "product.type.label", mproty."status" AS "product.type.status",
				mproty."mtime" AS "product.type.mtime", mproty."editor" AS "product.type.editor",
				mproty."ctime" AS "product.type.ctime"
			FROM "mshop_product_type" AS mproty
			:joins
			WHERE :cond
			GROUP BY mproty."id", mproty."siteid", mproty."code", mproty."domain",
				mproty."label", mproty."status", mproty."mtime", mproty."editor",
				mproty."ctime" /*-columns*/ , :columns /*columns-*/
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
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_product_type_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

