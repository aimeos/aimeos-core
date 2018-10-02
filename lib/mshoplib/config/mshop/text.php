<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


return array(
	'manager' => array(
		'lists' => array(
			'type' => array(
				'standard' => array(
					'delete' => array(
						'ansi' => '
							DELETE FROM "mshop_text_list_type"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_text_list_type" (
								"code", "domain", "label", "pos", "status",
								"mtime", "editor", "siteid", "ctime"
							) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_text_list_type"
							SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
								"status" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT mtexlity."id" AS "text.lists.type.id", mtexlity."siteid" AS "text.lists.type.siteid",
								mtexlity."code" AS "text.lists.type.code", mtexlity."domain" AS "text.lists.type.domain",
								mtexlity."label" AS "text.lists.type.label", mtexlity."status" AS "text.lists.type.status",
								mtexlity."mtime" AS "text.lists.type.mtime", mtexlity."editor" AS "text.lists.type.editor",
								mtexlity."ctime" AS "text.lists.type.ctime", mtexlity."pos" AS "text.lists.type.position"
							FROM "mshop_text_list_type" AS mtexlity
							:joins
							WHERE :cond
							GROUP BY mtexlity."id", mtexlity."siteid", mtexlity."code", mtexlity."domain",
								mtexlity."label", mtexlity."status", mtexlity."mtime", mtexlity."editor",
								mtexlity."ctime", mtexlity."pos" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						'
					),
					'count' => array(
						'ansi' => '
							SELECT COUNT(*) AS "count"
							FROM (
								SELECT DISTINCT mtexlity."id"
								FROM "mshop_text_list_type" as mtexlity
								:joins
								WHERE :cond
								LIMIT 10000 OFFSET 0
							) AS list
						'
					),
					'newid' => array(
						'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
						'mysql' => 'SELECT LAST_INSERT_ID()',
						'oracle' => 'SELECT mshop_text_list_type_seq.CURRVAL FROM DUAL',
						'pgsql' => 'SELECT lastval()',
						'sqlite' => 'SELECT last_insert_rowid()',
						'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
						'sqlanywhere' => 'SELECT @@IDENTITY',
					),
				),
			),
			'standard' => array(
				'aggregate' => array(
					'ansi' => '
						SELECT "key", COUNT("id") AS "count"
						FROM (
							SELECT :key AS "key", mtexli."id" AS "id"
							FROM "mshop_text_list" AS mtexli
							:joins
							WHERE :cond
							GROUP BY :key, mtexli."id" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY "key"
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_text_list"
						WHERE :cond AND siteid = ?
					'
				),
				'getposmax' => array(
					'ansi' => '
						SELECT MAX( "pos" ) AS pos
						FROM "mshop_text_list"
						WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
							AND "domain" = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_text_list" (
							"parentid", "typeid", "domain", "refid", "start", "end",
							"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_text_list"
						SET "parentid"=?, "typeid" = ?, "domain" = ?, "refid" = ?, "start" = ?, "end" = ?,
							"config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'updatepos' => array(
					'ansi' => '
						UPDATE "mshop_text_list"
							SET "pos" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'move' => array(
					'ansi' => '
						UPDATE "mshop_text_list"
							SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
							AND "domain" = ? AND "pos" >= ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mtexli."id" AS "text.lists.id", mtexli."parentid" AS "text.lists.parentid",
							mtexli."siteid" AS "text.lists.siteid", mtexli."typeid" AS "text.lists.typeid",
							mtexli."domain" AS "text.lists.domain", mtexli."refid" AS "text.lists.refid",
							mtexli."start" AS "text.lists.datestart", mtexli."end" AS "text.lists.dateend",
							mtexli."config" AS "text.lists.config", mtexli."pos" AS "text.lists.position",
							mtexli."status" AS "text.lists.status", mtexli."mtime" AS "text.lists.mtime",
							mtexli."editor" AS "text.lists.editor", mtexli."ctime" AS "text.lists.ctime"
						FROM "mshop_text_list" AS mtexli
						:joins
						WHERE :cond
						GROUP BY mtexli."id", mtexli."parentid", mtexli."siteid", mtexli."typeid",
							mtexli."domain", mtexli."refid", mtexli."start", mtexli."end",
							mtexli."config", mtexli."pos", mtexli."status", mtexli."mtime",
							mtexli."editor", mtexli."ctime" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mtexli."id"
							FROM "mshop_text_list" AS mtexli
							:joins
							WHERE :cond
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_text_list_seq.CURRVAL FROM DUAL',
					'pgsql' => 'SELECT lastval()',
					'sqlite' => 'SELECT last_insert_rowid()',
					'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
		),
		'type' => array(
			'standard' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_text_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_text_type" (
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_text_type"
						SET "code"=?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?,"mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mtexty."id" AS "text.type.id", mtexty."siteid" AS "text.type.siteid",
							mtexty."code" AS "text.type.code", mtexty."domain" AS "text.type.domain",
							mtexty."label" AS "text.type.label", mtexty."status" AS "text.type.status",
							mtexty."mtime" AS "text.type.mtime", mtexty."editor" AS "text.type.editor",
							mtexty."ctime" AS "text.type.ctime", mtexty."pos" AS "text.type.position"
						FROM "mshop_text_type" mtexty
						:joins
						WHERE :cond
						GROUP BY mtexty."id", mtexty."siteid", mtexty."code", mtexty."domain",
							mtexty."label", mtexty."status", mtexty."mtime", mtexty."editor",
							mtexty."ctime", mtexty."pos" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mtexty."id"
							FROM "mshop_text_type" mtexty
							:joins
							WHERE :cond
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_text_type_seq.CURRVAL FROM DUAL',
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
					DELETE FROM "mshop_text"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_text" (
						"langid", "typeid", "domain", "label", "content",
						"status", "mtime", "editor", "siteid", "ctime"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_text"
					SET "langid" = ?, "typeid" = ?, "domain" = ?, "label" = ?,
						"content" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT mtex."id" AS "text.id", mtex."siteid" AS "text.siteid",
						mtex."langid" AS "text.languageid",	mtex."typeid" AS "text.typeid",
						mtex."domain" AS "text.domain", mtex."label" AS "text.label",
						mtex."content" AS "text.content", mtex."status" AS "text.status",
						mtex."mtime" AS "text.mtime", mtex."editor" AS "text.editor",
						mtex."ctime" AS "text.ctime"
					FROM "mshop_text" AS mtex
					:joins
					WHERE :cond
					GROUP BY mtex."id", mtex."siteid", mtex."langid",	mtex."typeid",
						mtex."domain", mtex."label", mtex."content", mtex."status",
						mtex."mtime", mtex."editor", mtex."ctime" /*-columns*/ , :columns /*columns-*/
					/*-orderby*/ ORDER BY :order /*orderby-*/
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT DISTINCT mtex."id"
						FROM "mshop_text" AS mtex
						:joins
						WHERE :cond
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_text_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
	),
);