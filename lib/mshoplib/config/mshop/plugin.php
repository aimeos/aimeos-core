<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


return array(
	'provider' => array(
		'order' => array(
			'decorators' => array(
				'Log', 'Singleton',
			),
		),
	),
	'manager' => array(
		'type' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_plugin_type"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_plugin_type" ( :names
						"code", "domain", "label", "pos", "status",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_plugin_type"
					SET :names
						"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
						"status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mpluty."id" AS "plugin.type.id", mpluty."siteid" AS "plugin.type.siteid",
						mpluty."code" AS "plugin.type.code", mpluty."domain" AS "plugin.type.domain",
						mpluty."label" AS "plugin.type.label", mpluty."status" AS "plugin.type.status",
						mpluty."mtime" AS "plugin.type.mtime", mpluty."editor" AS "plugin.type.editor",
						mpluty."ctime" AS "plugin.type.ctime", mpluty."pos" AS "plugin.type.position"
					FROM "mshop_plugin_type" mpluty
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mpluty."id" AS "plugin.type.id", mpluty."siteid" AS "plugin.type.siteid",
						mpluty."code" AS "plugin.type.code", mpluty."domain" AS "plugin.type.domain",
						mpluty."label" AS "plugin.type.label", mpluty."status" AS "plugin.type.status",
						mpluty."mtime" AS "plugin.type.mtime", mpluty."editor" AS "plugin.type.editor",
						mpluty."ctime" AS "plugin.type.ctime", mpluty."pos" AS "plugin.type.position"
					FROM "mshop_plugin_type" mpluty
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
						SELECT mpluty."id"
						FROM "mshop_plugin_type" mpluty
						:joins
						WHERE :cond
						ORDER BY mpluty."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mpluty."id"
						FROM "mshop_plugin_type" mpluty
						:joins
						WHERE :cond
						ORDER BY mpluty."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_plugin_type_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_plugin"
				WHERE :cond AND siteid = ?
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_plugin" ( :names
					"type", "label", "provider", "config", "pos",
					"status", "mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_plugin"
				SET :names
					"type" = ?, "label" = ?, "provider" = ?, "config" = ?,
					"pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
					mplu."id" AS "plugin.id", mplu."siteid" AS "plugin.siteid",
					mplu."type" AS "plugin.type", mplu."label" AS "plugin.label",
					mplu."provider" AS "plugin.provider", mplu."config" AS "plugin.config",
					mplu."pos" AS "plugin.position", mplu."status" AS "plugin.status",
					mplu."mtime" AS "plugin.mtime", mplu."editor" AS "plugin.editor",
					mplu."ctime" AS "plugin.ctime"
				FROM "mshop_plugin" mplu
				:joins
				WHERE :cond
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					mplu."id" AS "plugin.id", mplu."siteid" AS "plugin.siteid",
					mplu."type" AS "plugin.type", mplu."label" AS "plugin.label",
					mplu."provider" AS "plugin.provider", mplu."config" AS "plugin.config",
					mplu."pos" AS "plugin.position", mplu."status" AS "plugin.status",
					mplu."mtime" AS "plugin.mtime", mplu."editor" AS "plugin.editor",
					mplu."ctime" AS "plugin.ctime"
				FROM "mshop_plugin" mplu
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
					SELECT mplu."id"
					FROM "mshop_plugin" mplu
					:joins
					WHERE :cond
					ORDER BY mplu."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mplu."id"
					FROM "mshop_plugin" mplu
					:joins
					WHERE :cond
					ORDER BY mplu."id"
					LIMIT 10000 OFFSET 0
				) AS list
			'
		),
		'newid' => array(
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT mshop_plugin_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
