<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


return array(
	'manager' => array(
		'code' => array(
			'standard' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_coupon_code"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_coupon_code" (
							"parentid", "code", "start", "end", "count", "ref",
							"mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_coupon_code"
						SET "parentid" = ?, "code" = ?, "start" = ?, "end" = ?,
							"count" = ?, "ref" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mcouco."id" AS "coupon.code.id", mcouco."parentid" AS "coupon.code.parentid",
							mcouco."siteid" AS "coupon.code.siteid", mcouco."code" AS "coupon.code.code",
							mcouco."start" AS "coupon.code.datestart", mcouco."end" AS "coupon.code.dateend",
							mcouco."count" AS "coupon.code.count", mcouco."ref" AS "coupon.code.ref",
							mcouco."mtime" AS "coupon.code.mtime", mcouco."ctime" AS "coupon.code.ctime",
							mcouco."editor" AS "coupon.code.editor"
						FROM "mshop_coupon_code" AS mcouco
						:joins
						WHERE :cond
						GROUP BY mcouco."id", mcouco."parentid", mcouco."siteid", mcouco."code",
							mcouco."start", mcouco."end", mcouco."count", mcouco."ref", mcouco."mtime",
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
			),
		),
		'standard' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_coupon"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_coupon" (
						"label", "provider", "config", "start", "end",
						"status", "mtime", "editor", "siteid", "ctime"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_coupon"
					SET "label" = ?, "provider" = ?, "config" = ?, "start" = ?, "end" = ?,
						"status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
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
		),
	),
);