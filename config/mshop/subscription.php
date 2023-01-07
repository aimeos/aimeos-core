<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2023
 */


return array(
	'manager' => array(
		'aggregate' => array(
			'ansi' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :val AS "val"
					FROM "mshop_subscription" msub
					:joins
					WHERE :cond
					GROUP BY msub.id, :cols, :val
					ORDER BY msub.id DESC
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				) AS list
				GROUP BY :keys
			',
			'mysql' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :val AS "val"
					FROM "mshop_subscription" msub
					:joins
					WHERE :cond
					GROUP BY msub.id, :cols, :val
					ORDER BY msub.id DESC
					LIMIT :size OFFSET :start
				) AS list
				GROUP BY :keys
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_subscription" ( :names
					"orderid", "ordprodid", "next", "end", "interval", "productid", "period",
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
					"orderid" = ?, "ordprodid" = ?, "next" = ?, "end" = ?, "interval" = ?,
					"productid" = ?, "period" = ?, "reason" = ?, "status" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" LIKE ? AND "id" = ?
			'
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_subscription"
				WHERE :cond AND "siteid" LIKE ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
					msub."id" AS "subscription.id", msub."orderid" AS "subscription.orderid",
					msub."ordprodid" AS "subscription.ordprodid", msub."siteid" AS "subscription.siteid",
					msub."next" AS "subscription.datenext", msub."end" AS "subscription.dateend",
					msub."interval" AS "subscription.interval", msub."reason" AS "subscription.reason",
					msub."productid" AS "subscription.productid", msub."period" AS "subscription.period",
					msub."status" AS "subscription.status", msub."ctime" AS "subscription.ctime",
					msub."mtime" AS "subscription.mtime", msub."editor" AS "subscription.editor"
				FROM "mshop_subscription" msub
				JOIN "mshop_order" mord ON msub."orderid" = mord."id"
				:joins
				WHERE :cond
				GROUP BY :columns :group
					msub."id", msub."orderid", msub."ordprodid", msub."siteid", msub."next", msub."end",
					msub."interval", msub."reason", msub."productid", msub."period", msub."status", msub."ctime",
					msub."mtime", msub."editor"
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					msub."id" AS "subscription.id", msub."orderid" AS "subscription.orderid",
					msub."ordprodid" AS "subscription.ordprodid", msub."siteid" AS "subscription.siteid",
					msub."next" AS "subscription.datenext", msub."end" AS "subscription.dateend",
					msub."interval" AS "subscription.interval", msub."reason" AS "subscription.reason",
					msub."productid" AS "subscription.productid", msub."period" AS "subscription.period",
					msub."status" AS "subscription.status", msub."ctime" AS "subscription.ctime",
					msub."mtime" AS "subscription.mtime", msub."editor" AS "subscription.editor"
				FROM "mshop_subscription" msub
				JOIN "mshop_order" mord ON msub."orderid" = mord."id"
				:joins
				WHERE :cond
				GROUP BY :group msub."id"
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT msub."id"
					FROM "mshop_subscription" msub
					JOIN "mshop_order" mord ON msub."orderid" = mord."id"
					:joins
					WHERE :cond
					GROUP BY msub."id"
					ORDER BY msub."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT msub."id"
					FROM "mshop_subscription" msub
					JOIN "mshop_order" mord ON msub."orderid" = mord."id"
					:joins
					WHERE :cond
					GROUP BY msub."id"
					ORDER BY msub."id"
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
