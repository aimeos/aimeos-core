<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


return array(
	'manager' => array(
		'lists' => array(
			'type' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_text_list_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_text_list_type" ( :names
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_text_list_type"
						SET :names
							"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mtexlity."id" AS "text.lists.type.id", mtexlity."siteid" AS "text.lists.type.siteid",
							mtexlity."code" AS "text.lists.type.code", mtexlity."domain" AS "text.lists.type.domain",
							mtexlity."label" AS "text.lists.type.label", mtexlity."status" AS "text.lists.type.status",
							mtexlity."mtime" AS "text.lists.type.mtime", mtexlity."editor" AS "text.lists.type.editor",
							mtexlity."ctime" AS "text.lists.type.ctime", mtexlity."pos" AS "text.lists.type.position"
						FROM "mshop_text_list_type" AS mtexlity
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mtexlity."id" AS "text.lists.type.id", mtexlity."siteid" AS "text.lists.type.siteid",
							mtexlity."code" AS "text.lists.type.code", mtexlity."domain" AS "text.lists.type.domain",
							mtexlity."label" AS "text.lists.type.label", mtexlity."status" AS "text.lists.type.status",
							mtexlity."mtime" AS "text.lists.type.mtime", mtexlity."editor" AS "text.lists.type.editor",
							mtexlity."ctime" AS "text.lists.type.ctime", mtexlity."pos" AS "text.lists.type.position"
						FROM "mshop_text_list_type" AS mtexlity
						:joins
						WHERE :cond
						ORDER BY :order
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mtexlity."id"
							FROM "mshop_text_list_type" as mtexlity
							:joins
							WHERE :cond
							ORDER BY mtexlity."id"
							OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mtexlity."id"
							FROM "mshop_text_list_type" as mtexlity
							:joins
							WHERE :cond
							ORDER BY mtexlity."id"
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
					'sqlsrv' => 'SELECT @@IDENTITY',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
			'aggregate' => array(
				'ansi' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :val AS "val"
						FROM "mshop_text_list" AS mtexli
						:joins
						WHERE :cond
						GROUP BY :cols, mtexli."id"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY :keys
				',
				'mysql' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :val AS "val"
						FROM "mshop_text_list" AS mtexli
						:joins
						WHERE :cond
						GROUP BY :cols, mtexli."id"
						ORDER BY :order
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_text_list"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_text_list" ( :names
						"parentid", "key", "type", "domain", "refid", "start", "end",
						"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_text_list"
					SET :names
						"parentid"=?, "key" = ?, "type" = ?, "domain" = ?, "refid" = ?, "start" = ?,
						"end" = ?, "config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mtexli."id" AS "text.lists.id", mtexli."parentid" AS "text.lists.parentid",
						mtexli."siteid" AS "text.lists.siteid", mtexli."type" AS "text.lists.type",
						mtexli."domain" AS "text.lists.domain", mtexli."refid" AS "text.lists.refid",
						mtexli."start" AS "text.lists.datestart", mtexli."end" AS "text.lists.dateend",
						mtexli."config" AS "text.lists.config", mtexli."pos" AS "text.lists.position",
						mtexli."status" AS "text.lists.status", mtexli."mtime" AS "text.lists.mtime",
						mtexli."editor" AS "text.lists.editor", mtexli."ctime" AS "text.lists.ctime"
					FROM "mshop_text_list" AS mtexli
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mtexli."id" AS "text.lists.id", mtexli."parentid" AS "text.lists.parentid",
						mtexli."siteid" AS "text.lists.siteid", mtexli."type" AS "text.lists.type",
						mtexli."domain" AS "text.lists.domain", mtexli."refid" AS "text.lists.refid",
						mtexli."start" AS "text.lists.datestart", mtexli."end" AS "text.lists.dateend",
						mtexli."config" AS "text.lists.config", mtexli."pos" AS "text.lists.position",
						mtexli."status" AS "text.lists.status", mtexli."mtime" AS "text.lists.mtime",
						mtexli."editor" AS "text.lists.editor", mtexli."ctime" AS "text.lists.ctime"
					FROM "mshop_text_list" AS mtexli
					:joins
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mtexli."id"
						FROM "mshop_text_list" AS mtexli
						:joins
						WHERE :cond
						ORDER BY mtexli."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mtexli."id"
						FROM "mshop_text_list" AS mtexli
						:joins
						WHERE :cond
						ORDER BY mtexli."id"
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
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'type' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_text_type"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_text_type" ( :names
						"code", "domain", "label", "pos", "status",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_text_type"
					SET :names
						"code"=?, "domain" = ?, "label" = ?, "pos" = ?,
						"status" = ?,"mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mtexty."id" AS "text.type.id", mtexty."siteid" AS "text.type.siteid",
						mtexty."code" AS "text.type.code", mtexty."domain" AS "text.type.domain",
						mtexty."label" AS "text.type.label", mtexty."status" AS "text.type.status",
						mtexty."mtime" AS "text.type.mtime", mtexty."editor" AS "text.type.editor",
						mtexty."ctime" AS "text.type.ctime", mtexty."pos" AS "text.type.position"
					FROM "mshop_text_type" mtexty
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mtexty."id" AS "text.type.id", mtexty."siteid" AS "text.type.siteid",
						mtexty."code" AS "text.type.code", mtexty."domain" AS "text.type.domain",
						mtexty."label" AS "text.type.label", mtexty."status" AS "text.type.status",
						mtexty."mtime" AS "text.type.mtime", mtexty."editor" AS "text.type.editor",
						mtexty."ctime" AS "text.type.ctime", mtexty."pos" AS "text.type.position"
					FROM "mshop_text_type" mtexty
					:joins
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mtexty."id"
						FROM "mshop_text_type" mtexty
						:joins
						WHERE :cond
						ORDER BY mtexty."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mtexty."id"
						FROM "mshop_text_type" mtexty
						:joins
						WHERE :cond
						ORDER BY mtexty."id"
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
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_text"
				WHERE :cond AND siteid = ?
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_text" ( :names
					"langid", "type", "domain", "label", "content",
					"status", "mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_text"
				SET :names
					"langid" = ?, "type" = ?, "domain" = ?, "label" = ?,
					"content" = ?, "status" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
					mtex."id" AS "text.id", mtex."siteid" AS "text.siteid",
					mtex."langid" AS "text.languageid",	mtex."type" AS "text.type",
					mtex."domain" AS "text.domain", mtex."label" AS "text.label",
					mtex."content" AS "text.content", mtex."status" AS "text.status",
					mtex."mtime" AS "text.mtime", mtex."editor" AS "text.editor",
					mtex."ctime" AS "text.ctime"
				FROM "mshop_text" AS mtex
				:joins
				WHERE :cond
				GROUP BY :columns :group
					mtex."id", mtex."siteid", mtex."langid",	mtex."type", mtex."domain", mtex."label",
					mtex."content", mtex."status", mtex."mtime", mtex."editor", mtex."ctime"
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					mtex."id" AS "text.id", mtex."siteid" AS "text.siteid",
					mtex."langid" AS "text.languageid",	mtex."type" AS "text.type",
					mtex."domain" AS "text.domain", mtex."label" AS "text.label",
					mtex."content" AS "text.content", mtex."status" AS "text.status",
					mtex."mtime" AS "text.mtime", mtex."editor" AS "text.editor",
					mtex."ctime" AS "text.ctime"
				FROM "mshop_text" AS mtex
				:joins
				WHERE :cond
				GROUP BY :group mtex."id"
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mtex."id"
					FROM "mshop_text" AS mtex
					:joins
					WHERE :cond
					GROUP BY mtex."id"
					ORDER BY mtex."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mtex."id"
					FROM "mshop_text" AS mtex
					:joins
					WHERE :cond
					GROUP BY mtex."id"
					ORDER BY mtex."id"
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
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
