<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


return array(
	'manager' => array(
		'standard' => array(
			'aggregate' => array(
				'ansi' => '
					SELECT "key", COUNT("val") AS "count"
					FROM (
						SELECT :key AS "key", :val AS "val"
						FROM "mshop_subscription" AS msub
						:joins
						WHERE :cond
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY "key"
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_subscription" (
						"ordbaseid", "ordprodid", "next", "end", "interval",
						"status", "mtime", "editor", "siteid", "ctime"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_subscription"
					SET "ordbaseid" = ?, "ordprodid" = ?, "next" = ?, "end" = ?, "interval" = ?,
						"status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_subscription"
					WHERE :cond AND siteid = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT msub."id" AS "subscription.id", msub."ordbaseid" AS "subscription.ordbaseid",
						msub."ordprodid" AS "subscription.ordprodid", msub."siteid" AS "subscription.siteid",
						msub."next" AS "subscription.datenext", msub."end" AS "subscription.dateend",
						msub."interval" AS "subscription.interval", msub."status" AS "subscription.status",
						msub."ctime" AS "subscription.ctime", msub."mtime" AS "subscription.mtime",
						msub."editor" AS "subscription.editor"
					FROM "mshop_subscription" AS msub
					:joins
					WHERE :cond
					GROUP BY msub."id", msub."ordbaseid", msub."ordprodid", msub."siteid", msub."next",
						msub."end", msub."interval", msub."status", msub."ctime", msub."mtime", msub."editor"
						/*-columns*/ , :columns /*columns-*/
					/*-subscriptionby*/ORDER BY :subscription/*subscriptionby-*/
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT( DISTINCT msub."id" ) AS "count"
					FROM "mshop_subscription" AS msub
					:joins
					WHERE :cond
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_subscription_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
	),
);
