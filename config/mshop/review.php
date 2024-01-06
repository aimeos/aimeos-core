<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2024
 */


return array(
	'manager' => array(
		'aggregate' => array(
			'ansi' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :val AS "val"
					FROM "mshop_review" mrev
					:joins
					WHERE :cond
					ORDER BY mrev.id DESC
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				) AS list
				GROUP BY :keys
			',
			'mysql' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :val AS "val"
					FROM "mshop_review" mrev
					:joins
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				) AS list
				GROUP BY :keys
			'
		),
		'aggregaterate' => array(
			'ansi' => '
				SELECT :keys, SUM("val") AS "sum", COUNT(*) AS "count"
				FROM (
					SELECT :acols, mrev.rating AS "val"
					FROM "mshop_review" mrev
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				) AS list
				GROUP BY :keys
			',
			'mysql' => '
				SELECT :keys, SUM("val") AS "sum", COUNT(*) AS "count"
				FROM (
					SELECT :acols, mrev.rating AS "val"
					FROM "mshop_review" mrev
					:joins
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				) AS list
				GROUP BY :keys
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_review" ( :names
					"domain", "refid", "customerid", "ordprodid", "name", "comment", "response",
					"rating", "status", "mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_review"
				SET :names
					"domain" = ?, "refid" = ?, "customerid" = ?, "ordprodid" = ?, "name" = ?,
					"comment" = ?, "response" = ?, "rating" = ?, "status" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" LIKE ? AND "id" = ?
			'
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_review"
				WHERE :cond AND "siteid" LIKE ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
				FROM "mshop_review" mrev
				:joins
				WHERE :cond
				GROUP BY :group
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
				FROM "mshop_review" mrev
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
					SELECT mrev."id"
					FROM "mshop_review" mrev
					:joins
					WHERE :cond
					GROUP BY mrev."id"
					ORDER BY mrev."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mrev."id"
					FROM "mshop_review" mrev
					:joins
					WHERE :cond
					GROUP BY mrev."id"
					ORDER BY mrev."id"
					LIMIT 10000 OFFSET 0
				) AS list
			'
		),
		'newid' => array(
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT mshop_review_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
