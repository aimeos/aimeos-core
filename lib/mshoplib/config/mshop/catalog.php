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
						DELETE FROM "mshop_catalog_list_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_catalog_list_type" ( :names
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_catalog_list_type"
						SET :names
							"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mcatlity."id" AS "catalog.lists.type.id", mcatlity."siteid" AS "catalog.lists.type.siteid",
							mcatlity."code" AS "catalog.lists.type.code", mcatlity."domain" AS "catalog.lists.type.domain",
							mcatlity."label" AS "catalog.lists.type.label", mcatlity."mtime" AS "catalog.lists.type.mtime",
							mcatlity."editor" AS "catalog.lists.type.editor", mcatlity."ctime" AS "catalog.lists.type.ctime",
							mcatlity."status" AS "catalog.lists.type.status", mcatlity."pos" AS "catalog.lists.type.position"
						FROM "mshop_catalog_list_type" AS mcatlity
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mcatlity."id" AS "catalog.lists.type.id", mcatlity."siteid" AS "catalog.lists.type.siteid",
							mcatlity."code" AS "catalog.lists.type.code", mcatlity."domain" AS "catalog.lists.type.domain",
							mcatlity."label" AS "catalog.lists.type.label", mcatlity."mtime" AS "catalog.lists.type.mtime",
							mcatlity."editor" AS "catalog.lists.type.editor", mcatlity."ctime" AS "catalog.lists.type.ctime",
							mcatlity."status" AS "catalog.lists.type.status", mcatlity."pos" AS "catalog.lists.type.position"
						FROM "mshop_catalog_list_type" AS mcatlity
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
							SELECT mcatlity."id"
							FROM "mshop_catalog_list_type" AS mcatlity
							:joins
							WHERE :cond
							ORDER BY mcatlity."id"
							OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mcatlity."id"
							FROM "mshop_catalog_list_type" AS mcatlity
							:joins
							WHERE :cond
							ORDER BY mcatlity."id"
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_catalog_list_type_seq.CURRVAL FROM DUAL',
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
						FROM "mshop_catalog_list" AS mcatli
						:joins
						WHERE :cond
						GROUP BY :cols, mcatli."id"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY :keys
				',
				'mysql' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :val AS "val"
						FROM "mshop_catalog_list" AS mcatli
						:joins
						WHERE :cond
						GROUP BY :cols, mcatli."id"
						ORDER BY :order
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_catalog_list"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_catalog_list" ( :names
						"parentid", "key", "type", "domain", "refid", "start", "end",
						"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_catalog_list"
					SET :names
							"parentid" = ?, "key" = ?, "type" = ?, "domain" = ?, "refid" = ?, "start" = ?,
							"end" = ?, "config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mcatli."id" AS "catalog.lists.id", mcatli."parentid" AS "catalog.lists.parentid",
						mcatli."siteid" AS "catalog.lists.siteid", mcatli."type" AS "catalog.lists.type",
						mcatli."domain" AS "catalog.lists.domain", mcatli."refid" AS "catalog.lists.refid",
						mcatli."start" AS "catalog.lists.datestart", mcatli."end" AS "catalog.lists.dateend",
						mcatli."config" AS "catalog.lists.config", mcatli."pos" AS "catalog.lists.position",
						mcatli."status" AS "catalog.lists.status", mcatli."mtime" AS "catalog.lists.mtime",
						mcatli."editor" AS "catalog.lists.editor", mcatli."ctime" AS "catalog.lists.ctime"
					FROM "mshop_catalog_list" AS mcatli
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mcatli."id" AS "catalog.lists.id", mcatli."parentid" AS "catalog.lists.parentid",
						mcatli."siteid" AS "catalog.lists.siteid", mcatli."type" AS "catalog.lists.type",
						mcatli."domain" AS "catalog.lists.domain", mcatli."refid" AS "catalog.lists.refid",
						mcatli."start" AS "catalog.lists.datestart", mcatli."end" AS "catalog.lists.dateend",
						mcatli."config" AS "catalog.lists.config", mcatli."pos" AS "catalog.lists.position",
						mcatli."status" AS "catalog.lists.status", mcatli."mtime" AS "catalog.lists.mtime",
						mcatli."editor" AS "catalog.lists.editor", mcatli."ctime" AS "catalog.lists.ctime"
					FROM "mshop_catalog_list" AS mcatli
					USE INDEX (unq_mscatli_pid_dm_sid_ty_rid, idx_mscatli_pid_dm_sid_pos_rid, idx_mscatli_rid_dom_sid_ty, idx_mscatli_key_sid)
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
						SELECT mcatli."id"
						FROM "mshop_catalog_list" AS mcatli
						:joins
						WHERE :cond
						ORDER BY mcatli."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mcatli."id"
						FROM "mshop_catalog_list" AS mcatli
						:joins
						WHERE :cond
						ORDER BY mcatli."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_catalog_list_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'cleanup' => array(
			'ansi' => '
				DELETE FROM "mshop_catalog"
				WHERE :siteid AND "nleft" >= ? AND "nright" <= ?
			'
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_catalog"
				WHERE "siteid" = :siteid AND "nleft" >= ? AND "nright" <= ?
			'
		),
		'get' => array(
			'ansi' => '
				SELECT :columns
					mcat."id", mcat."code", mcat."url", mcat."label", mcat."config",
					mcat."status", mcat."level", mcat."parentid", mcat."siteid",
					mcat."nleft" AS "left", mcat."nright" AS "right",
					mcat."mtime", mcat."editor", mcat."ctime", mcat."target"
				FROM "mshop_catalog" AS mcat, "mshop_catalog" AS parent
				WHERE mcat."siteid" = :siteid AND mcat."nleft" >= parent."nleft"
					AND mcat."nleft" <= parent."nright"
					AND parent."siteid" = :siteid AND parent."id" = ?
					AND mcat."level" <= parent."level" + ? AND :cond
				GROUP BY :columns
					mcat."id", mcat."code", mcat."url", mcat."label", mcat."config",
					mcat."status", mcat."level", mcat."parentid", mcat."siteid",
					mcat."nleft", mcat."nright", mcat."target",
					mcat."mtime", mcat."editor", mcat."ctime"
				ORDER BY mcat."nleft"
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_catalog" (
					"siteid", "label", "code", "status", "parentid", "level",
					"nleft", "nright", "config", "mtime", "ctime", "editor", "target"
				) VALUES (
					:siteid, ?, ?, ?, ?, ?, ?, ?, \'\', \'1970-01-01 00:00:00\', \'1970-01-01 00:00:00\', \'\', \'\'
				)
			'
		),
		'insert-usage' => array(
			'ansi' => '
				UPDATE "mshop_catalog"
				SET :names "url" = ?, "config" = ?, "mtime" = ?, "editor" = ?, "target" = ?, "ctime" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_catalog"
				SET "label" = ?, "code" = ?, "status" = ?
				WHERE "siteid" = :siteid AND "id" = ?
			'
		),
		'update-parentid' => array(
			'ansi' => '
				UPDATE "mshop_catalog"
				SET "parentid" = ?
				WHERE "siteid" = :siteid AND "id" = ?
			'
		),
		'update-usage' => array(
			'ansi' => '
				UPDATE "mshop_catalog"
				SET "url" = ?, "config" = ?, "mtime" = ?, "editor" = ?, "target" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'move-left' => array(
			'ansi' => '
				UPDATE "mshop_catalog"
				SET "nleft" = "nleft" + ?, "level" = "level" + ?
				WHERE "siteid" = :siteid AND "nleft" >= ? AND "nleft" <= ?
			'
		),
		'move-right' => array(
			'ansi' => '
				UPDATE "mshop_catalog"
				SET "nright" = "nright" + ?
				WHERE "siteid" = :siteid AND "nright" >= ? AND "nright" <= ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
					mcat."id", mcat."code", mcat."url", mcat."label", mcat."config",
					mcat."status", mcat."level", mcat."parentid", mcat."siteid",
					mcat."nleft" AS "left", mcat."nright" AS "right",
					mcat."mtime", mcat."editor", mcat."ctime", mcat."target"
				FROM "mshop_catalog" AS mcat
				WHERE mcat."siteid" = :siteid AND mcat."nleft" >= ?
					AND mcat."nright" <= ? AND :cond
				ORDER BY :order
			'
		),
		'search-item' => array(
			'ansi' => '
				SELECT :columns
					mcat."id", mcat."code", mcat."url", mcat."label", mcat."config",
					mcat."status", mcat."level", mcat."parentid", mcat."siteid",
					mcat."nleft" AS "left", mcat."nright" AS "right",
					mcat."mtime", mcat."editor", mcat."ctime", mcat."target"
				FROM "mshop_catalog" AS mcat
				:joins
				WHERE :cond
				GROUP BY :columns :group
					mcat."id", mcat."code", mcat."url", mcat."label", mcat."config",
					mcat."status", mcat."level", mcat."parentid", mcat."siteid",
					mcat."nleft", mcat."nright", mcat."mtime", mcat."editor",
					mcat."ctime", mcat."target"
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					mcat."id", mcat."code", mcat."url", mcat."label", mcat."config",
					mcat."status", mcat."level", mcat."parentid", mcat."siteid",
					mcat."nleft" AS "left", mcat."nright" AS "right",
					mcat."mtime", mcat."editor", mcat."ctime", mcat."target"
				FROM "mshop_catalog" AS mcat
				:joins
				WHERE :cond
				GROUP BY :group mcat."id"
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mcat."id"
					FROM "mshop_catalog" AS mcat
					:joins
					WHERE :cond
					GROUP BY mcat."id"
					ORDER BY mcat."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mcat."id"
					FROM "mshop_catalog" AS mcat
					:joins
					WHERE :cond
					GROUP BY mcat."id"
					ORDER BY mcat."id"
					LIMIT 10000 OFFSET 0
				) AS list
			'
		),
		'newid' => array(
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT mshop_catalog_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
		'lock' => array(
			'db2' => 'LOCK TABLE "mshop_catalog" IN EXCLUSIVE MODE',
			'mysql' => "DO GET_LOCK('aimeos.catalog', -1)", // LOCK TABLE implicit commits transactions
			'oracle' => 'LOCK TABLE "mshop_catalog" IN EXCLUSIVE MODE',
			'pgsql' => 'LOCK TABLE ONLY "mshop_catalog" IN EXCLUSIVE MODE',
			'sqlanywhere' => 'LOCK TABLE "mshop_catalog" IN EXCLUSIVE MODE',
			'sqlsrv' => "EXEC sp_getapplock @Resource = 'aimeos.catalog', @LockMode = 'Exclusive'",
		),
		'unlock' => array(
			'mysql' => "DO RELEASE_LOCK('aimeos.catalog')",
			'sqlsrv' => "EXEC sp_releaseapplock @Resource = 'aimeos.catalog'",
		),
	),
);
