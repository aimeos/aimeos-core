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
						DELETE FROM "mshop_service_list_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_service_list_type" ( :names
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_service_list_type"
						SET :names
							"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mserlity."id" AS "service.lists.type.id", mserlity."siteid" AS "service.lists.type.siteid",
							mserlity."code" AS "service.lists.type.code", mserlity."domain" AS "service.lists.type.domain",
							mserlity."label" AS "service.lists.type.label", mserlity."status" AS "service.lists.type.status",
							mserlity."mtime" AS "service.lists.type.mtime", mserlity."editor" AS "service.lists.type.editor",
							mserlity."ctime" AS "service.lists.type.ctime", mserlity."pos" AS "service.lists.type.position"
						FROM "mshop_service_list_type" AS mserlity
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mserlity."id" AS "service.lists.type.id", mserlity."siteid" AS "service.lists.type.siteid",
							mserlity."code" AS "service.lists.type.code", mserlity."domain" AS "service.lists.type.domain",
							mserlity."label" AS "service.lists.type.label", mserlity."status" AS "service.lists.type.status",
							mserlity."mtime" AS "service.lists.type.mtime", mserlity."editor" AS "service.lists.type.editor",
							mserlity."ctime" AS "service.lists.type.ctime", mserlity."pos" AS "service.lists.type.position"
						FROM "mshop_service_list_type" AS mserlity
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
							SELECT mserlity."id"
							FROM "mshop_service_list_type" as mserlity
							:joins
							WHERE :cond
							ORDER BY mserlity."id"
							OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mserlity."id"
							FROM "mshop_service_list_type" as mserlity
							:joins
							WHERE :cond
							ORDER BY mserlity."id"
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
					'sqlsrv' => 'SELECT @@IDENTITY',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
			'aggregate' => array(
				'ansi' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :val AS "val"
						FROM "mshop_service_list" AS mserli
						:joins
						WHERE :cond
						GROUP BY :cols, mserli."id"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY :keys
				',
				'mysql' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :val AS "val"
						FROM "mshop_service_list" AS mserli
						:joins
						WHERE :cond
						GROUP BY :cols, mserli."id"
						ORDER BY :order
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
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
					INSERT INTO "mshop_service_list" ( :names
						"parentid", "key", "type", "domain", "refid", "start", "end",
						"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_service_list"
					SET :names
						"parentid"=?, "key" = ?, "type" = ?, "domain" = ?, "refid" = ?, "start" = ?,
						"end" = ?, "config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mserli."id" AS "service.lists.id", mserli."parentid" AS "service.lists.parentid",
						mserli."siteid" AS "service.lists.siteid", mserli."type" AS "service.lists.type",
						mserli."domain" AS "service.lists.domain", mserli."refid" AS "service.lists.refid",
						mserli."start" AS "service.lists.datestart", mserli."end" AS "service.lists.dateend",
						mserli."config" AS "service.lists.config", mserli."pos" AS "service.lists.position",
						mserli."status" AS "service.lists.status", mserli."mtime" AS "service.lists.mtime",
						mserli."editor" AS "service.lists.editor", mserli."ctime" AS "service.lists.ctime"
					FROM "mshop_service_list" AS mserli
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mserli."id" AS "service.lists.id", mserli."parentid" AS "service.lists.parentid",
						mserli."siteid" AS "service.lists.siteid", mserli."type" AS "service.lists.type",
						mserli."domain" AS "service.lists.domain", mserli."refid" AS "service.lists.refid",
						mserli."start" AS "service.lists.datestart", mserli."end" AS "service.lists.dateend",
						mserli."config" AS "service.lists.config", mserli."pos" AS "service.lists.position",
						mserli."status" AS "service.lists.status", mserli."mtime" AS "service.lists.mtime",
						mserli."editor" AS "service.lists.editor", mserli."ctime" AS "service.lists.ctime"
					FROM "mshop_service_list" AS mserli
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
						SELECT mserli."id"
						FROM "mshop_service_list" AS mserli
						:joins
						WHERE :cond
						ORDER BY mserli."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mserli."id"
						FROM "mshop_service_list" AS mserli
						:joins
						WHERE :cond
						ORDER BY mserli."id"
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
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'type' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_service_type"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_service_type" ( :names
						"code", "domain", "label", "pos", "status",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_service_type"
					SET :names
						"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
						"status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mserty."id" AS "service.type.id", mserty."siteid" AS "service.type.siteid",
						mserty."domain" AS "service.type.domain", mserty."code" AS "service.type.code",
						mserty."label" AS "service.type.label", mserty."status" AS "service.type.status",
						mserty."mtime" AS "service.type.mtime", mserty."editor" AS "service.type.editor",
						mserty."ctime" AS "service.type.ctime", mserty."pos" AS "service.type.position"
					FROM "mshop_service_type" AS mserty
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mserty."id" AS "service.type.id", mserty."siteid" AS "service.type.siteid",
						mserty."domain" AS "service.type.domain", mserty."code" AS "service.type.code",
						mserty."label" AS "service.type.label", mserty."status" AS "service.type.status",
						mserty."mtime" AS "service.type.mtime", mserty."editor" AS "service.type.editor",
						mserty."ctime" AS "service.type.ctime", mserty."pos" AS "service.type.position"
					FROM "mshop_service_type" AS mserty
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
						SELECT mserty."id"
						FROM "mshop_service_type" AS mserty
						:joins
						WHERE :cond
						ORDER BY mserty."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mserty."id"
						FROM "mshop_service_type" AS mserty
						:joins
						WHERE :cond
						ORDER BY mserty."id"
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
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_service"
				WHERE :cond AND siteid = ?
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_service" ( :names
					"pos", "type", "code", "label", "provider", "start", "end",
					"config", "status", "mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_service"
				SET :names
					"pos" = ?, "type" = ?, "code" = ?, "label" = ?, "provider" = ?, "start" = ?,
					"end" = ?, "config" = ?, "status" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
					mser."id" AS "service.id", mser."siteid" AS "service.siteid",
					mser."pos" AS "service.position", mser."type" AS "service.type",
					mser."code" AS "service.code", mser."label" AS "service.label",
					mser."provider" AS "service.provider", mser."config" AS "service.config",
					mser."start" AS "service.datestart", mser."end" AS "service.dateend",
					mser."status" AS "service.status", mser."mtime" AS "service.mtime",
					mser."editor" AS "service.editor",	mser."ctime" AS "service.ctime"
				FROM "mshop_service" AS mser
				:joins
				WHERE :cond
				GROUP BY :columns :group
					mser."id", mser."siteid", mser."pos", mser."type", mser."code", mser."label",
					mser."provider", mser."config", mser."start", mser."end", mser."status", mser."mtime",
					mser."editor",	mser."ctime"
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					mser."id" AS "service.id", mser."siteid" AS "service.siteid",
					mser."pos" AS "service.position", mser."type" AS "service.type",
					mser."code" AS "service.code", mser."label" AS "service.label",
					mser."provider" AS "service.provider", mser."config" AS "service.config",
					mser."start" AS "service.datestart", mser."end" AS "service.dateend",
					mser."status" AS "service.status", mser."mtime" AS "service.mtime",
					mser."editor" AS "service.editor",	mser."ctime" AS "service.ctime"
				FROM "mshop_service" AS mser
				:joins
				WHERE :cond
				GROUP BY :group mser."id"
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT count(*) as "count"
				FROM (
					SELECT mser."id"
					FROM "mshop_service" AS mser
					:joins
					WHERE :cond
					GROUP BY mser."id"
					ORDER BY mser."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT count(*) as "count"
				FROM (
					SELECT mser."id"
					FROM "mshop_service" AS mser
					:joins
					WHERE :cond
					GROUP BY mser."id"
					ORDER BY mser."id"
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
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
