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
							DELETE FROM "mshop_service_list_type"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_service_list_type" (
								"code", "domain", "label", "pos", "status",
								"mtime", "editor", "siteid", "ctime"
							) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_service_list_type"
							SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
								"status" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT mserlity."id" AS "service.lists.type.id", mserlity."siteid" AS "service.lists.type.siteid",
								mserlity."code" AS "service.lists.type.code", mserlity."domain" AS "service.lists.type.domain",
								mserlity."label" AS "service.lists.type.label", mserlity."status" AS "service.lists.type.status",
								mserlity."mtime" AS "service.lists.type.mtime", mserlity."editor" AS "service.lists.type.editor",
								mserlity."ctime" AS "service.lists.type.ctime", mserlity."pos" AS "service.lists.type.position"
							FROM "mshop_service_list_type" AS mserlity
							:joins
							WHERE :cond
							GROUP BY mserlity."id", mserlity."siteid", mserlity."code", mserlity."domain",
								mserlity."label", mserlity."status", mserlity."mtime", mserlity."editor",
								mserlity."ctime", mserlity."pos" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						'
					),
					'count' => array(
						'ansi' => '
							SELECT COUNT(*) AS "count"
							FROM (
								SELECT DISTINCT mserlity."id"
								FROM "mshop_service_list_type" as mserlity
								:joins
								WHERE :cond
								LIMIT 10000 OFFSET 0
							) AS list
						'
					),
					'newid' => array(
						'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
						'mysql' => 'SELECT LAST_INSERT_ID()',
						'oracle' => 'SELECT mshop_service_list_type_seq.CURRVAL FROM DUAL',
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
							SELECT :key AS "key", mserli."id" AS "id"
							FROM "mshop_service_list" AS mserli
							:joins
							WHERE :cond
							GROUP BY :key, mserli."id" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY "key"
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_service_list"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_service_list" (
							"parentid", "typeid", "domain", "refid", "start", "end",
							"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_service_list"
						SET "parentid"=?, "typeid" = ?, "domain" = ?, "refid" = ?, "start" = ?, "end" = ?,
							"config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'updatepos' => array(
					'ansi' => '
						UPDATE "mshop_service_list"
							SET "pos" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'move' => array(
					'ansi' => '
						UPDATE "mshop_service_list"
							SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
							AND "domain" = ? AND "pos" >= ?
					'
				),
				'getposmax' => array(
					'ansi' => '
						SELECT MAX( "pos" ) AS pos
						FROM "mshop_service_list"
						WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
							AND "domain" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mserli."id" AS "service.lists.id", mserli."parentid" AS "service.lists.parentid",
							mserli."siteid" AS "service.lists.siteid", mserli."typeid" AS "service.lists.typeid",
							mserli."domain" AS "service.lists.domain", mserli."refid" AS "service.lists.refid",
							mserli."start" AS "service.lists.datestart", mserli."end" AS "service.lists.dateend",
							mserli."config" AS "service.lists.config", mserli."pos" AS "service.lists.position",
							mserli."status" AS "service.lists.status", mserli."mtime" AS "service.lists.mtime",
							mserli."editor" AS "service.lists.editor", mserli."ctime" AS "service.lists.ctime"
						FROM "mshop_service_list" AS mserli
						:joins
						WHERE :cond
						GROUP BY mserli."id", mserli."parentid", mserli."siteid", mserli."typeid",
							mserli."domain", mserli."refid", mserli."start", mserli."end",
							mserli."config", mserli."pos", mserli."status", mserli."mtime",
							mserli."editor", mserli."ctime" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mserli."id"
							FROM "mshop_service_list" AS mserli
							:joins
							WHERE :cond
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_service_list_seq.CURRVAL FROM DUAL',
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
						DELETE FROM "mshop_service_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_service_type" (
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_service_type"
						SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mserty."id" AS "service.type.id", mserty."siteid" AS "service.type.siteid",
							mserty."domain" AS "service.type.domain", mserty."code" AS "service.type.code",
							mserty."label" AS "service.type.label", mserty."status" AS "service.type.status",
							mserty."mtime" AS "service.type.mtime", mserty."editor" AS "service.type.editor",
							mserty."ctime" AS "service.type.ctime", mserty."pos" AS "service.type.position"
						FROM "mshop_service_type" AS mserty
						:joins
						WHERE :cond
						GROUP BY mserty."id", mserty."siteid", mserty."domain", mserty."code",
							mserty."label", mserty."status", mserty."mtime", mserty."editor",
							mserty."ctime", mserty."pos" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mserty."id"
							FROM "mshop_service_type" AS mserty
							:joins
							WHERE :cond
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_service_type_seq.CURRVAL FROM DUAL',
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
					DELETE FROM "mshop_service"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_service" (
						"pos", "typeid", "code", "label", "provider", "start", "end",
						"config", "status", "mtime", "editor", "siteid", "ctime"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_service"
					SET "pos" = ?, "typeid" = ?, "code" = ?, "label" = ?, "provider" = ?,
						"start" = ?, "end" = ?, "config" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT mser."id" AS "service.id", mser."siteid" AS "service.siteid",
						mser."pos" AS "service.position", mser."typeid" AS "service.typeid",
						mser."code" AS "service.code", mser."label" AS "service.label",
						mser."provider" AS "service.provider", mser."config" AS "service.config",
						mser."start" AS "service.datestart", mser."end" AS "service.dateend",
						mser."status" AS "service.status", mser."mtime" AS "service.mtime",
						mser."editor" AS "service.editor",	mser."ctime" AS "service.ctime"
					FROM "mshop_service" AS mser
					:joins
					WHERE :cond
					GROUP BY mser."id", mser."siteid", mser."pos", mser."typeid",
						mser."code", mser."label", mser."provider", mser."config",
						mser."start", mser."end", mser."status",
						mser."mtime", mser."ctime", mser."editor"
						/*-columns*/ , :columns /*columns-*/
					/*-orderby*/ ORDER BY :order /*orderby-*/
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT count(*) as "count"
					FROM (
						SELECT DISTINCT mser."id"
						FROM "mshop_service" AS mser
						:joins
						WHERE :cond
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_service_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
	),
);