<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_coupon_code"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_coupon_code" (
				"siteid", "parentid", "code", "count", "start", "end", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_coupon_code"
			SET "siteid" = ?, "parentid" = ?, "code" = ?, "count" = ?,
				"start" = ?, "end" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mcouco."id" AS "coupon.code.id", mcouco."parentid" AS "coupon.code.parentid",
				mcouco."siteid" AS "coupon.code.siteid", mcouco."code" AS "coupon.code.code",
				mcouco."start" AS "coupon.code.datestart", mcouco."end" AS "coupon.code.dateend",
				mcouco."count" AS "coupon.code.count", mcouco."mtime" AS "coupon.code.mtime",
				mcouco."editor" AS "coupon.code.editor", mcouco."ctime" AS "coupon.code.ctime"
			FROM "mshop_coupon_code" AS mcouco
			:joins
			WHERE :cond
			GROUP BY mcouco."id", mcouco."parentid", mcouco."siteid", mcouco."code",
				mcouco."start", mcouco."end", mcouco."count", mcouco."mtime",
				mcouco."editor", mcouco."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcouco."id"
				FROM "mshop_coupon_code" AS mcouco
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'counter' => array(
		'ansi' => '
			UPDATE "mshop_coupon_code"
			SET	"count" = "count" + ?, "mtime" = ?, "editor" = ?
			WHERE :cond AND "code" = ?
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_coupon_code_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);
