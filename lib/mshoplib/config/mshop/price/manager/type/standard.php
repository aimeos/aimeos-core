<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_price_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_price_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_price_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mprity."id" AS "price.type.id", mprity."siteid" AS "price.type.siteid",
				mprity."code" AS "price.type.code", mprity."domain" AS "price.type.domain",
				mprity."label" AS "price.type.label", mprity."status" AS "price.type.status",
				mprity."mtime" AS "price.type.mtime", mprity."editor" AS "price.type.editor",
				mprity."ctime" AS "price.type.ctime"
			FROM "mshop_price_type" AS mprity
			:joins
			WHERE :cond
			GROUP BY mprity."id", mprity."siteid", mprity."code", mprity."domain",
				mprity."label", mprity."status", mprity."mtime", mprity."editor",
				mprity."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mprity."id"
				FROM "mshop_price_type" AS mprity
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_price_type_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

