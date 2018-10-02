<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


return array(
	'manager' => array(
		'type' => array(
			'standard' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_tag_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_tag_type" (
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_tag_type"
						SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mtagty."id" AS "tag.type.id", mtagty."siteid" AS "tag.type.siteid",
							mtagty."code" AS "tag.type.code", mtagty."domain" AS "tag.type.domain",
							mtagty."label" AS "tag.type.label", mtagty."status" AS "tag.type.status",
							mtagty."mtime" AS "tag.type.mtime", mtagty."editor" AS "tag.type.editor",
							mtagty."ctime" AS "tag.type.ctime", mtagty."pos" AS "tag.type.position"
						FROM "mshop_tag_type" mtagty
						:joins
						WHERE :cond
						GROUP BY mtagty."id", mtagty."siteid", mtagty."code", mtagty."domain",
							mtagty."label", mtagty."status", mtagty."mtime", mtagty."editor",
							mtagty."ctime", mtagty."pos" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mtagty."id"
							FROM "mshop_tag_type" mtagty
							:joins
							WHERE :cond
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
					'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
		),
		'standard' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_tag"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_tag" (
						"langid", "typeid", "domain", "label",
						"mtime", "editor", "siteid", "ctime"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_tag"
					SET "langid" = ?, "typeid" = ?, "domain" = ?,
						"label" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT mtag."id" AS "tag.id", mtag."siteid" AS "tag.siteid",
						mtag."typeid" AS "tag.typeid", mtag."langid" AS "tag.languageid",
						mtag."domain" AS "tag.domain", mtag."label" AS "tag.label",
						mtag."mtime" AS "tag.mtime", mtag."editor" AS "tag.editor",
						mtag."ctime" AS "tag.ctime"
					FROM "mshop_tag" AS mtag
					:joins
					WHERE :cond
					GROUP BY mtag."id", mtag."siteid", mtag."typeid", mtag."langid",
						mtag."domain", mtag."label", mtag."mtime", mtag."editor", mtag."ctime"
						/*-columns*/ , :columns /*columns-*/
					/*-orderby*/ ORDER BY :order /*orderby-*/
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT DISTINCT mtag."id"
						FROM "mshop_tag" AS mtag
						:joins
						WHERE :cond
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
				'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
	),
);