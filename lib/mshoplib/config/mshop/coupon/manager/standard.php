<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_coupon"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_coupon" (
				"siteid", "label", "provider", "config", "start", "end",
				"status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_coupon"
			SET "siteid" = ?, "label" = ?, "provider" = ?, "config" = ?,
				"start" = ?, "end" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mcou."id" AS "coupon.id", mcou."siteid" AS "coupon.siteid",
				mcou."label" AS "coupon.label", mcou."provider" AS "coupon.provider",
				mcou."start" AS "coupon.datestart", mcou."end" AS "coupon.dateend",
				mcou."config" AS "coupon.config", mcou."status" AS "coupon.status",
				mcou."mtime" AS "coupon.mtime", mcou."editor" AS "coupon.editor",
				mcou."ctime" AS "coupon.ctime"
			FROM "mshop_coupon" AS mcou
			:joins
			WHERE :cond
			GROUP BY mcou."id", mcou."siteid", mcou."label", mcou."provider",
				mcou."start", mcou."end", mcou."config", mcou."status",
				mcou."mtime", mcou."editor", mcou."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcou."id"
				FROM "mshop_coupon" AS mcou
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_coupon_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

