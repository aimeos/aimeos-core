<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


return array(
	'manager' => array(
		'type' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_rule_type"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_rule_type" ( :names
						"code", "domain", "label", "pos", "status",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_rule_type"
					SET :names
						"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
						"status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mrulty."id" AS "rule.type.id", mrulty."siteid" AS "rule.type.siteid",
						mrulty."code" AS "rule.type.code", mrulty."domain" AS "rule.type.domain",
						mrulty."label" AS "rule.type.label", mrulty."status" AS "rule.type.status",
						mrulty."mtime" AS "rule.type.mtime", mrulty."editor" AS "rule.type.editor",
						mrulty."ctime" AS "rule.type.ctime", mrulty."pos" AS "rule.type.position"
					FROM "mshop_rule_type" mrulty
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mrulty."id" AS "rule.type.id", mrulty."siteid" AS "rule.type.siteid",
						mrulty."code" AS "rule.type.code", mrulty."domain" AS "rule.type.domain",
						mrulty."label" AS "rule.type.label", mrulty."status" AS "rule.type.status",
						mrulty."mtime" AS "rule.type.mtime", mrulty."editor" AS "rule.type.editor",
						mrulty."ctime" AS "rule.type.ctime", mrulty."pos" AS "rule.type.position"
					FROM "mshop_rule_type" mrulty
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
						SELECT mrulty."id"
						FROM "mshop_rule_type" mrulty
						:joins
						WHERE :cond
						ORDER BY mrulty."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mrulty."id"
						FROM "mshop_rule_type" mrulty
						:joins
						WHERE :cond
						ORDER BY mrulty."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_rule_type_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_rule"
				WHERE :cond AND siteid = ?
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_rule" ( :names
					"type", "label", "provider", "config", "start", "end", "pos",
					"status", "mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_rule"
				SET :names
					"type" = ?, "label" = ?, "provider" = ?, "config" = ?, "start" = ?, "end" = ?,
					"pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
					mrul."id" AS "rule.id", mrul."siteid" AS "rule.siteid",
					mrul."type" AS "rule.type", mrul."label" AS "rule.label",
					mrul."provider" AS "rule.provider", mrul."config" AS "rule.config",
					mrul."start" AS "rule.datestart", mrul."end" AS "rule.dateend",
					mrul."pos" AS "rule.position", mrul."status" AS "rule.status",
					mrul."mtime" AS "rule.mtime", mrul."editor" AS "rule.editor",
					mrul."ctime" AS "rule.ctime"
				FROM "mshop_rule" mrul
				:joins
				WHERE :cond
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					mrul."id" AS "rule.id", mrul."siteid" AS "rule.siteid",
					mrul."type" AS "rule.type", mrul."label" AS "rule.label",
					mrul."provider" AS "rule.provider", mrul."config" AS "rule.config",
					mrul."start" AS "rule.datestart", mrul."end" AS "rule.dateend",
					mrul."pos" AS "rule.position", mrul."status" AS "rule.status",
					mrul."mtime" AS "rule.mtime", mrul."editor" AS "rule.editor",
					mrul."ctime" AS "rule.ctime"
				FROM "mshop_rule" mrul
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
					SELECT mrul."id"
					FROM "mshop_rule" mrul
					:joins
					WHERE :cond
					ORDER BY mrul."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mrul."id"
					FROM "mshop_rule" mrul
					:joins
					WHERE :cond
					ORDER BY mrul."id"
					LIMIT 10000 OFFSET 0
				) AS list
			'
		),
		'newid' => array(
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT mshop_rule_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
