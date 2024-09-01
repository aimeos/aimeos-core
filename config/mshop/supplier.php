<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


return array(
	'manager' => array(
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_supplier"
				WHERE :cond AND "siteid" LIKE ?
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_supplier" ( :names
					"code", "label", "pos", "status", "mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_supplier"
				SET :names
					"code" = ?, "label" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" LIKE ? AND "id" = ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
				FROM "mshop_supplier" msup
				:joins
				WHERE :cond
				GROUP BY :group
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
				FROM "mshop_supplier" msup
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
					SELECT msup."id"
					FROM "mshop_supplier" msup
					:joins
					WHERE :cond
					GROUP BY msup."id"
					ORDER BY msup."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT msup."id"
					FROM "mshop_supplier" msup
					:joins
					WHERE :cond
					GROUP BY msup."id"
					ORDER BY msup."id"
					LIMIT 10000 OFFSET 0
				) AS list
			'
		),
		'newid' => array(
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT mshop_supplier_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
