<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_product_list_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_product_list_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_product_list_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mprolity."id" AS "product.lists.type.id", mprolity."siteid" AS "product.lists.type.siteid",
				mprolity."code" AS "product.lists.type.code", mprolity."domain" AS "product.lists.type.domain",
				mprolity."label" AS "product.lists.type.label", mprolity."status" AS "product.lists.type.status",
				mprolity."mtime" AS "product.lists.type.mtime", mprolity."editor" AS "product.lists.type.editor",
				mprolity."ctime" AS "product.lists.type.ctime"
			FROM "mshop_product_list_type" AS mprolity
			:joins
			WHERE :cond
			GROUP BY mprolity."id", mprolity."siteid", mprolity."code", mprolity."domain",
				mprolity."label", mprolity."status", mprolity."mtime", mprolity."editor",
				mprolity."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mprolity."id"
				FROM "mshop_product_list_type" AS mprolity
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_product_list_type_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

