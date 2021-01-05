<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


return array(
	'manager' => array(
		'type' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_tag_type"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_tag_type" ( :names
						"code", "domain", "label", "pos", "status",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_tag_type"
					SET :names
						"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
						"status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mtagty."id" AS "tag.type.id", mtagty."siteid" AS "tag.type.siteid",
						mtagty."code" AS "tag.type.code", mtagty."domain" AS "tag.type.domain",
						mtagty."label" AS "tag.type.label", mtagty."status" AS "tag.type.status",
						mtagty."mtime" AS "tag.type.mtime", mtagty."editor" AS "tag.type.editor",
						mtagty."ctime" AS "tag.type.ctime", mtagty."pos" AS "tag.type.position"
					FROM "mshop_tag_type" mtagty
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mtagty."id" AS "tag.type.id", mtagty."siteid" AS "tag.type.siteid",
						mtagty."code" AS "tag.type.code", mtagty."domain" AS "tag.type.domain",
						mtagty."label" AS "tag.type.label", mtagty."status" AS "tag.type.status",
						mtagty."mtime" AS "tag.type.mtime", mtagty."editor" AS "tag.type.editor",
						mtagty."ctime" AS "tag.type.ctime", mtagty."pos" AS "tag.type.position"
					FROM "mshop_tag_type" mtagty
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
						SELECT mtagty."id"
						FROM "mshop_tag_type" mtagty
						:joins
						WHERE :cond
						ORDER BY mtagty."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mtagty."id"
						FROM "mshop_tag_type" mtagty
						:joins
						WHERE :cond
						ORDER BY mtagty."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_tag_type_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_tag"
				WHERE :cond AND siteid = ?
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_tag" ( :names
					"langid", "type", "domain", "label",
					"mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_tag"
				SET :names
					"langid" = ?, "type" = ?, "domain" = ?, "label" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
					mtag."id" AS "tag.id", mtag."siteid" AS "tag.siteid",
					mtag."type" AS "tag.type", mtag."langid" AS "tag.languageid",
					mtag."domain" AS "tag.domain", mtag."label" AS "tag.label",
					mtag."mtime" AS "tag.mtime", mtag."editor" AS "tag.editor",
					mtag."ctime" AS "tag.ctime"
				FROM "mshop_tag" AS mtag
				:joins
				WHERE :cond
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					mtag."id" AS "tag.id", mtag."siteid" AS "tag.siteid",
					mtag."type" AS "tag.type", mtag."langid" AS "tag.languageid",
					mtag."domain" AS "tag.domain", mtag."label" AS "tag.label",
					mtag."mtime" AS "tag.mtime", mtag."editor" AS "tag.editor",
					mtag."ctime" AS "tag.ctime"
				FROM "mshop_tag" AS mtag
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
					SELECT mtag."id"
					FROM "mshop_tag" AS mtag
					:joins
					WHERE :cond
					ORDER BY mtag."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mtag."id"
					FROM "mshop_tag" AS mtag
					:joins
					WHERE :cond
					ORDER BY mtag."id"
					LIMIT 10000 OFFSET 0
				) AS list
			'
		),
		'newid' => array(
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT mshop_tag_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
