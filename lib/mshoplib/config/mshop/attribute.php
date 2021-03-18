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
						DELETE FROM "mshop_attribute_list_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_attribute_list_type"( :names
							"code", "domain", "label", "pos", "status",
							"mtime","editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_attribute_list_type"
						SET :names
							"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mattlity."id" AS "attribute.lists.type.id", mattlity."siteid" AS "attribute.lists.type.siteid",
							mattlity."code" AS "attribute.lists.type.code", mattlity."domain" AS "attribute.lists.type.domain",
							mattlity."label" AS "attribute.lists.type.label", mattlity."status" AS "attribute.lists.type.status",
							mattlity."mtime" AS "attribute.lists.type.mtime", mattlity."ctime" AS "attribute.lists.type.ctime",
							mattlity."editor" AS "attribute.lists.type.editor", mattlity."pos" AS "attribute.lists.type.position"
						FROM "mshop_attribute_list_type" AS mattlity
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mattlity."id" AS "attribute.lists.type.id", mattlity."siteid" AS "attribute.lists.type.siteid",
							mattlity."code" AS "attribute.lists.type.code", mattlity."domain" AS "attribute.lists.type.domain",
							mattlity."label" AS "attribute.lists.type.label", mattlity."status" AS "attribute.lists.type.status",
							mattlity."mtime" AS "attribute.lists.type.mtime", mattlity."ctime" AS "attribute.lists.type.ctime",
							mattlity."editor" AS "attribute.lists.type.editor", mattlity."pos" AS "attribute.lists.type.position"
						FROM "mshop_attribute_list_type" AS mattlity
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
							SELECT mattlity."id"
							FROM "mshop_attribute_list_type" AS mattlity
							:joins
							WHERE :cond
							ORDER BY mattlity."id"
							OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mattlity."id"
							FROM "mshop_attribute_list_type" AS mattlity
							:joins
							WHERE :cond
							ORDER BY mattlity."id"
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_attribute_list_type_seq.CURRVAL FROM DUAL',
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
						FROM "mshop_attribute_list" AS mattli
						:joins
						WHERE :cond
						GROUP BY :cols, mattli."id"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY :keys
				',
				'mysql' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :val AS "val"
						FROM "mshop_attribute_list" AS mattli
						:joins
						WHERE :cond
						GROUP BY :cols, mattli."id"
						ORDER BY :order
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_attribute_list"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_attribute_list" ( :names
						"parentid", "key", "type", "domain", "refid", "start", "end",
						"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_attribute_list"
					SET :names
						"parentid" = ?, "key" = ?, "type" = ?, "domain" = ?, "refid" = ?, "start" = ?,
						"end" = ?, "config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mattli."id" AS "attribute.lists.id", mattli."siteid" AS "attribute.lists.siteid",
						mattli."parentid" AS "attribute.lists.parentid", mattli."type" AS "attribute.lists.type",
						mattli."domain" AS "attribute.lists.domain", mattli."refid" AS "attribute.lists.refid",
						mattli."start" AS "attribute.lists.datestart", mattli."end" AS "attribute.lists.dateend",
						mattli."config" AS "attribute.lists.config", mattli."pos" AS "attribute.lists.position",
						mattli."status" AS "attribute.lists.status", mattli."mtime" AS "attribute.lists.mtime",
						mattli."ctime" AS "attribute.lists.ctime", mattli."editor" AS "attribute.lists.editor"
					FROM "mshop_attribute_list" AS mattli
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mattli."id" AS "attribute.lists.id", mattli."siteid" AS "attribute.lists.siteid",
						mattli."parentid" AS "attribute.lists.parentid", mattli."type" AS "attribute.lists.type",
						mattli."domain" AS "attribute.lists.domain", mattli."refid" AS "attribute.lists.refid",
						mattli."start" AS "attribute.lists.datestart", mattli."end" AS "attribute.lists.dateend",
						mattli."config" AS "attribute.lists.config", mattli."pos" AS "attribute.lists.position",
						mattli."status" AS "attribute.lists.status", mattli."mtime" AS "attribute.lists.mtime",
						mattli."ctime" AS "attribute.lists.ctime", mattli."editor" AS "attribute.lists.editor"
					FROM "mshop_attribute_list" AS mattli
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
						SELECT mattli."id"
						FROM "mshop_attribute_list" AS mattli
						:joins
						WHERE :cond
						ORDER BY mattli."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mattli."id"
						FROM "mshop_attribute_list" AS mattli
						:joins
						WHERE :cond
						ORDER BY mattli."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_attribute_list_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'property' => array(
			'type' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_attribute_property_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_attribute_property_type" ( :names
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_attribute_property_type"
						SET :names
							"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mattprty."id" AS "attribute.property.type.id", mattprty."siteid" AS "attribute.property.type.siteid",
							mattprty."code" AS "attribute.property.type.code", mattprty."domain" AS "attribute.property.type.domain",
							mattprty."label" AS "attribute.property.type.label", mattprty."status" AS "attribute.property.type.status",
							mattprty."mtime" AS "attribute.property.type.mtime", mattprty."editor" AS "attribute.property.type.editor",
							mattprty."ctime" AS "attribute.property.type.ctime", mattprty."pos" AS "attribute.property.type.position"
						FROM "mshop_attribute_property_type" mattprty
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mattprty."id" AS "attribute.property.type.id", mattprty."siteid" AS "attribute.property.type.siteid",
							mattprty."code" AS "attribute.property.type.code", mattprty."domain" AS "attribute.property.type.domain",
							mattprty."label" AS "attribute.property.type.label", mattprty."status" AS "attribute.property.type.status",
							mattprty."mtime" AS "attribute.property.type.mtime", mattprty."editor" AS "attribute.property.type.editor",
							mattprty."ctime" AS "attribute.property.type.ctime", mattprty."pos" AS "attribute.property.type.position"
						FROM "mshop_attribute_property_type" mattprty
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
							SELECT mattprty."id"
							FROM "mshop_attribute_property_type" mattprty
							:joins
							WHERE :cond
							ORDER BY mattprty."id"
							OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mattprty."id"
							FROM "mshop_attribute_property_type" mattprty
							:joins
							WHERE :cond
							ORDER BY mattprty."id"
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_attribute_property_type_seq.CURRVAL FROM DUAL',
					'pgsql' => 'SELECT lastval()',
					'sqlite' => 'SELECT last_insert_rowid()',
					'sqlsrv' => 'SELECT @@IDENTITY',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_attribute_property"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_attribute_property" ( :names
						"parentid", "key", "type", "langid", "value",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_attribute_property"
					SET :names
						"parentid" = ?, "key" = ?, "type" = ?, "langid" = ?,
						"value" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mattpr."id" AS "attribute.property.id", mattpr."parentid" AS "attribute.property.parentid",
						mattpr."siteid" AS "attribute.property.siteid", mattpr."type" AS "attribute.property.type",
						mattpr."langid" AS "attribute.property.languageid", mattpr."value" AS "attribute.property.value",
						mattpr."mtime" AS "attribute.property.mtime", mattpr."editor" AS "attribute.property.editor",
						mattpr."ctime" AS "attribute.property.ctime"
					FROM "mshop_attribute_property" AS mattpr
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mattpr."id" AS "attribute.property.id", mattpr."parentid" AS "attribute.property.parentid",
						mattpr."siteid" AS "attribute.property.siteid", mattpr."type" AS "attribute.property.type",
						mattpr."langid" AS "attribute.property.languageid", mattpr."value" AS "attribute.property.value",
						mattpr."mtime" AS "attribute.property.mtime", mattpr."editor" AS "attribute.property.editor",
						mattpr."ctime" AS "attribute.property.ctime"
					FROM "mshop_attribute_property" AS mattpr
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
						SELECT mattpr."id"
						FROM "mshop_attribute_property" AS mattpr
						:joins
						WHERE :cond
						ORDER BY mattpr."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mattpr."id"
						FROM "mshop_attribute_property" AS mattpr
						:joins
						WHERE :cond
						ORDER BY mattpr."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_attribute_property_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'type' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_attribute_type"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_attribute_type" ( :names
						"code", "domain", "label", "pos", "status",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_attribute_type"
					SET :names
						"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
						"status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mattty."id" AS "attribute.type.id", mattty."siteid" AS "attribute.type.siteid",
						mattty."code" AS "attribute.type.code", mattty."domain" AS "attribute.type.domain",
						mattty."label" AS "attribute.type.label", mattty."status" AS "attribute.type.status",
						mattty."mtime" AS "attribute.type.mtime", mattty."ctime" AS "attribute.type.ctime",
						mattty."editor" AS "attribute.type.editor", mattty."pos" AS "attribute.type.position"
					FROM "mshop_attribute_type" AS mattty
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mattty."id" AS "attribute.type.id", mattty."siteid" AS "attribute.type.siteid",
						mattty."code" AS "attribute.type.code", mattty."domain" AS "attribute.type.domain",
						mattty."label" AS "attribute.type.label", mattty."status" AS "attribute.type.status",
						mattty."mtime" AS "attribute.type.mtime", mattty."ctime" AS "attribute.type.ctime",
						mattty."editor" AS "attribute.type.editor", mattty."pos" AS "attribute.type.position"
					FROM "mshop_attribute_type" AS mattty
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
						SELECT mattty."id"
						FROM "mshop_attribute_type" AS mattty
						:joins
						WHERE :cond
						ORDER BY mattty."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mattty."id"
						FROM "mshop_attribute_type" AS mattty
						:joins
						WHERE :cond
						ORDER BY mattty."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_attribute_type_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_attribute"
				WHERE :cond AND siteid = ?
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_attribute" ( :names
					"key", "type", "domain", "code", "status", "pos",
					"label", "mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_attribute"
				SET :names
					"key" = ?, "type" = ?, "domain" = ?, "code" = ?, "status" = ?,
					"pos" = ?, "label" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
					matt."id" AS "attribute.id", matt."siteid" AS "attribute.siteid",
					matt."type" AS "attribute.type", matt."domain" AS "attribute.domain",
					matt."code" AS "attribute.code", matt."status" AS "attribute.status",
					matt."pos" AS "attribute.position", matt."label" AS "attribute.label",
					matt."mtime" AS "attribute.mtime", matt."ctime" AS "attribute.ctime",
					matt."editor" AS "attribute.editor"
				FROM "mshop_attribute" AS matt
				:joins
				WHERE :cond
				GROUP BY :columns :group
					matt."id", matt."siteid", matt."type", matt."domain", matt."code", matt."status",
					matt."pos", matt."label", matt."mtime", matt."ctime", matt."editor"
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					matt."id" AS "attribute.id", matt."siteid" AS "attribute.siteid",
					matt."type" AS "attribute.type", matt."domain" AS "attribute.domain",
					matt."code" AS "attribute.code", matt."status" AS "attribute.status",
					matt."pos" AS "attribute.position", matt."label" AS "attribute.label",
					matt."mtime" AS "attribute.mtime", matt."ctime" AS "attribute.ctime",
					matt."editor" AS "attribute.editor"
				FROM "mshop_attribute" AS matt
				:joins
				WHERE :cond
				GROUP BY :group matt."id"
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT matt."id"
					FROM "mshop_attribute" AS matt
					:joins
					WHERE :cond
					GROUP BY matt."id"
					ORDER BY matt."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT matt."id"
					FROM "mshop_attribute" AS matt
					:joins
					WHERE :cond
					GROUP BY matt."id"
					ORDER BY matt."id"
					LIMIT 10000 OFFSET 0
				) AS list
			'
		),
		'newid' => array(
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT mshop_attribute_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
