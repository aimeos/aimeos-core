<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


return array(
	'manager' => array(
		'lists' => [
			'submanagers' => [
				'type' => 'type',
			]
		],
		'submanagers' => [
			'lists' => 'lists',
		],
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
				FROM "mshop_catalog" mcat, "mshop_catalog" AS parent
				WHERE parent."id" = ?
					AND mcat."siteid" = :siteid
					AND parent."siteid" = :siteid
					AND mcat."nleft" >= parent."nleft"
					AND mcat."nleft" <= parent."nright"
					AND mcat."level" <= parent."level" + ?
					AND :cond
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
				WHERE "siteid" LIKE ? AND "id" = ?
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
				SET :names "url" = ?, "config" = ?, "mtime" = ?, "editor" = ?, "target" = ?
				WHERE "siteid" LIKE ? AND "id" = ?
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
				FROM "mshop_catalog" mcat
				WHERE mcat."siteid" = :siteid AND mcat."nleft" >= ?
					AND mcat."nright" <= ? AND :cond
				ORDER BY :order
			'
		),
		'search-item' => array(
			'ansi' => '
				SELECT :columns,
					mcat."id", mcat."code", mcat."url", mcat."label", mcat."config",
					mcat."status", mcat."level", mcat."parentid", mcat."siteid",
					mcat."nleft" AS "left", mcat."nright" AS "right",
					mcat."mtime", mcat."editor", mcat."ctime", mcat."target"
				FROM "mshop_catalog" mcat
				:joins
				WHERE :cond
				GROUP BY :group
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns,
					mcat."id", mcat."code", mcat."url", mcat."label", mcat."config",
					mcat."status", mcat."level", mcat."parentid", mcat."siteid",
					mcat."nleft" AS "left", mcat."nright" AS "right",
					mcat."mtime", mcat."editor", mcat."ctime", mcat."target"
				FROM "mshop_catalog" mcat
				:joins
				WHERE :cond
				GROUP BY :group
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mcat."id"
					FROM "mshop_catalog" mcat
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
					FROM "mshop_catalog" mcat
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
			'pgsql' => 'SET TRANSACTION ISOLATION LEVEL SERIALIZABLE',
			'sqlanywhere' => 'LOCK TABLE "mshop_catalog" IN EXCLUSIVE MODE',
			'sqlsrv' => "EXEC sp_getapplock @Resource = 'aimeos.catalog', @LockMode = 'Exclusive'",
		),
		'unlock' => array(
			'mysql' => "DO RELEASE_LOCK('aimeos.catalog')",
			'sqlsrv' => "EXEC sp_releaseapplock @Resource = 'aimeos.catalog'",
		),
	),
);
