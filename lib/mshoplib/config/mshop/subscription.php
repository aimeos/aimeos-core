<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


return array(
	'manager' => array(
		'aggregate' => array(
			'ansi' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :val AS "val"
					FROM "mshop_subscription" AS mord
					:joins
					WHERE :cond
					GROUP BY mord.id, :cols, :val
					ORDER BY mord.id DESC
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				) AS list
				GROUP BY :keys
			',
			'mysql' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :val AS "val"
					FROM "mshop_subscription" AS mord
					:joins
					WHERE :cond
					GROUP BY mord.id, :cols, :val
					ORDER BY mord.id DESC
					LIMIT :size OFFSET :start
				) AS list
				GROUP BY :keys
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_subscription" ( :names
					"baseid", "ordprodid", "next", "end", "interval", "productid", "period",
					"reason", "status", "mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_subscription"
				SET :names
					"baseid" = ?, "ordprodid" = ?, "next" = ?, "end" = ?, "interval" = ?,
					"productid" = ?, "period" = ?, "reason" = ?, "status" = ?, "mtime" = ?, "editor" = ?
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
				SELECT :columns
					mord."id" AS "subscription.id", mord."baseid" AS "subscription.ordbaseid",
					mord."ordprodid" AS "subscription.ordprodid", mord."siteid" AS "subscription.siteid",
					mord."next" AS "subscription.datenext", mord."end" AS "subscription.dateend",
					mord."interval" AS "subscription.interval", mord."reason" AS "subscription.reason",
					mord."productid" AS "subscription.productid", mord."period" AS "subscription.period",
					mord."status" AS "subscription.status", mord."ctime" AS "subscription.ctime",
					mord."mtime" AS "subscription.mtime", mord."editor" AS "subscription.editor"
				FROM "mshop_subscription" AS mord
				:joins
				WHERE :cond
				GROUP BY :columns :group
					mord."id", mord."baseid", mord."ordprodid", mord."siteid", mord."next", mord."end",
					mord."interval", mord."reason", mord."productid", mord."period", mord."status", mord."ctime",
					mord."mtime", mord."editor"
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					mord."id" AS "subscription.id", mord."baseid" AS "subscription.ordbaseid",
					mord."ordprodid" AS "subscription.ordprodid", mord."siteid" AS "subscription.siteid",
					mord."next" AS "subscription.datenext", mord."end" AS "subscription.dateend",
					mord."interval" AS "subscription.interval", mord."reason" AS "subscription.reason",
					mord."productid" AS "subscription.productid", mord."period" AS "subscription.period",
					mord."status" AS "subscription.status", mord."ctime" AS "subscription.ctime",
					mord."mtime" AS "subscription.mtime", mord."editor" AS "subscription.editor"
				FROM "mshop_subscription" AS mord
				:joins
				WHERE :cond
				GROUP BY :group mord."id"
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mord."id"
					FROM "mshop_subscription" AS mord
					:joins
					WHERE :cond
					GROUP BY mord."id"
					ORDER BY mord."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mord."id"
					FROM "mshop_subscription" AS mord
					:joins
					WHERE :cond
					GROUP BY mord."id"
					ORDER BY mord."id"
					LIMIT 10000 OFFSET 0
				) AS list
			'
		),
		'newid' => array(
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT mshop_subscription_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
