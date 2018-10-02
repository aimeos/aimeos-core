<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


return array(
	'manager' => array(
		'lists' => array(
			'type' => array(
				'standard' => array(
					'delete' => array(
						'ansi' => '
							DELETE FROM "mshop_attribute_list_type"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_attribute_list_type"(
								"code", "domain", "label", "pos", "status",
								"mtime","editor", "siteid", "ctime"
							) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_attribute_list_type"
							SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
								"status" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT mattlity."id" AS "attribute.lists.type.id", mattlity."siteid" AS "attribute.lists.type.siteid",
								mattlity."code" AS "attribute.lists.type.code", mattlity."domain" AS "attribute.lists.type.domain",
								mattlity."label" AS "attribute.lists.type.label", mattlity."status" AS "attribute.lists.type.status",
								mattlity."mtime" AS "attribute.lists.type.mtime", mattlity."ctime" AS "attribute.lists.type.ctime",
								mattlity."editor" AS "attribute.lists.type.editor", mattlity."pos" AS "attribute.lists.type.position"
							FROM "mshop_attribute_list_type" AS mattlity
							:joins
							WHERE :cond
							GROUP BY mattlity."id", mattlity."siteid", mattlity."code", mattlity."domain",
								mattlity."label", mattlity."status", mattlity."mtime", mattlity."ctime",
								mattlity."editor", mattlity."pos" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						'
					),
					'count' => array(
						'ansi' => '
							SELECT COUNT(*) AS "count"
							FROM (
								SELECT DISTINCT mattlity."id"
								FROM "mshop_attribute_list_type" AS mattlity
								:joins
								WHERE :cond
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
						'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
						'sqlanywhere' => 'SELECT @@IDENTITY',
					),
				),
			),
			'standard' => array(
				'aggregate' => array(
					'ansi' => '
						SELECT "key", COUNT("id") AS "count"
						FROM (
							SELECT :key AS "key", mattli."id" AS "id"
							FROM "mshop_attribute_list" AS mattli
							:joins
							WHERE :cond
							GROUP BY :key, mattli."id" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY "key"
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_attribute_list"
						WHERE :cond AND siteid = ?
					'
				),
				'getposmax' => array(
					'ansi' => '
						SELECT MAX( "pos" ) AS pos
						FROM "mshop_attribute_list"
						WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
							AND "domain" = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_attribute_list"(
							"parentid", "typeid", "domain", "refid", "start", "end",
							"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_attribute_list"
						SET "parentid" = ?, "typeid" = ?, "domain" = ?, "refid" = ?, "start" = ?, "end" = ?,
							"config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'updatepos' => array(
					'ansi' => '
						UPDATE "mshop_attribute_list"
						SET "pos" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'move' => array(
					'ansi' => '
						UPDATE "mshop_attribute_list"
						SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
							AND "domain" = ? AND "pos" >= ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mattli."id" AS "attribute.lists.id", mattli."siteid" AS "attribute.lists.siteid",
							mattli."parentid" AS "attribute.lists.parentid", mattli."typeid" AS "attribute.lists.typeid",
							mattli."domain" AS "attribute.lists.domain", mattli."refid" AS "attribute.lists.refid",
							mattli."start" AS "attribute.lists.datestart", mattli."end" AS "attribute.lists.dateend",
							mattli."config" AS "attribute.lists.config", mattli."pos" AS "attribute.lists.position",
							mattli."status" AS "attribute.lists.status", mattli."mtime" AS "attribute.lists.mtime",
							mattli."ctime" AS "attribute.lists.ctime", mattli."editor" AS "attribute.lists.editor"
						FROM "mshop_attribute_list" AS mattli
						:joins
						WHERE :cond
						GROUP BY mattli."id", mattli."siteid", mattli."parentid", mattli."typeid",
							mattli."domain", mattli."refid", mattli."start", mattli."end",
							mattli."config", mattli."pos", mattli."status", mattli."mtime",
							mattli."ctime", mattli."editor" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mattli."id"
							FROM "mshop_attribute_list" AS mattli
							:joins
							WHERE :cond
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
					'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
		),
		'property' => array(
			'type' => array(
				'standard' => array(
					'delete' => array(
						'ansi' => '
							DELETE FROM "mshop_attribute_property_type"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_attribute_property_type" (
								"code", "domain", "label", "pos", "status",
								"mtime", "editor", "siteid", "ctime"
							) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_attribute_property_type"
							SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
								"status" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT mattprty."id" AS "attribute.property.type.id", mattprty."siteid" AS "attribute.property.type.siteid",
								mattprty."code" AS "attribute.property.type.code", mattprty."domain" AS "attribute.property.type.domain",
								mattprty."label" AS "attribute.property.type.label", mattprty."status" AS "attribute.property.type.status",
								mattprty."mtime" AS "attribute.property.type.mtime", mattprty."editor" AS "attribute.property.type.editor",
								mattprty."ctime" AS "attribute.property.type.ctime", mattprty."pos" AS "attribute.property.type.position"
							FROM "mshop_attribute_property_type" mattprty
							:joins
							WHERE :cond
							GROUP BY mattprty."id", mattprty."siteid", mattprty."code", mattprty."domain",
								mattprty."label", mattprty."status", mattprty."mtime", mattprty."editor",
								mattprty."ctime", mattprty."pos" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						'
					),
					'count' => array(
						'ansi' => '
							SELECT COUNT(*) AS "count"
							FROM (
								SELECT DISTINCT mattprty."id"
								FROM "mshop_attribute_property_type" mattprty
								:joins
								WHERE :cond
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
						'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
						'sqlanywhere' => 'SELECT @@IDENTITY',
					),
				),
			),
			'standard' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_attribute_property"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_attribute_property" (
							"parentid", "typeid", "langid", "value",
							"mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_attribute_property"
						SET "parentid" = ?, "typeid" = ?, "langid" = ?,
							"value" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mattpr."id" AS "attribute.property.id", mattpr."parentid" AS "attribute.property.parentid",
							mattpr."siteid" AS "attribute.property.siteid", mattpr."typeid" AS "attribute.property.typeid",
							mattpr."langid" AS "attribute.property.languageid", mattpr."value" AS "attribute.property.value",
							mattpr."mtime" AS "attribute.property.mtime", mattpr."editor" AS "attribute.property.editor",
							mattpr."ctime" AS "attribute.property.ctime"
						FROM "mshop_attribute_property" AS mattpr
						:joins
						WHERE :cond
						GROUP BY mattpr."id", mattpr."parentid", mattpr."siteid", mattpr."typeid",
							mattpr."langid", mattpr."value", mattpr."mtime", mattpr."editor",
							mattpr."ctime" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mattpr."id"
							FROM "mshop_attribute_property" AS mattpr
							:joins
							WHERE :cond
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
					'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
		),
		'type' => array(
			'standard' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_attribute_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_attribute_type" (
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_attribute_type"
						SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mattty."id" AS "attribute.type.id", mattty."siteid" AS "attribute.type.siteid",
							mattty."code" AS "attribute.type.code", mattty."domain" AS "attribute.type.domain",
							mattty."label" AS "attribute.type.label", mattty."status" AS "attribute.type.status",
							mattty."mtime" AS "attribute.type.mtime", mattty."ctime" AS "attribute.type.ctime",
							mattty."editor" AS "attribute.type.editor", mattty."pos" AS "attribute.type.position"
						FROM "mshop_attribute_type" AS mattty
						:joins
						WHERE :cond
						GROUP BY mattty."id", mattty."siteid", mattty."code", mattty."domain",
							mattty."label", mattty."status", mattty."mtime", mattty."ctime",
							mattty."editor", mattty."pos" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mattty."id"
							FROM "mshop_attribute_type" AS mattty
							:joins
							WHERE :cond
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
					'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
		),
		'standard' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_attribute"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_attribute" (
						"typeid", "domain", "code", "status", "pos",
						"label", "mtime", "editor", "siteid", "ctime"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_attribute"
					SET "typeid" = ?, "domain" = ?, "code" = ?, "status" = ?,
						"pos" = ?, "label" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT matt."id" AS "attribute.id", matt."siteid" AS "attribute.siteid",
						matt."typeid" AS "attribute.typeid", matt."domain" AS "attribute.domain",
						matt."code" AS "attribute.code", matt."status" AS "attribute.status",
						matt."pos" AS "attribute.position", matt."label" AS "attribute.label",
						matt."mtime" AS "attribute.mtime", matt."ctime" AS "attribute.ctime",
						matt."editor" AS "attribute.editor"
					FROM "mshop_attribute" AS matt
					:joins
					WHERE :cond
					GROUP BY matt."id", matt."siteid", matt."typeid", matt."domain",
						matt."code", matt."status", matt."pos", matt."label",
						matt."mtime", matt."ctime", matt."editor" /*-columns*/ , :columns /*columns-*/
					/*-orderby*/ ORDER BY :order /*orderby-*/
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT DISTINCT matt."id"
						FROM "mshop_attribute" AS matt
						:joins
						WHERE :cond
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
				'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
	),
);