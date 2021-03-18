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
						DELETE FROM "mshop_media_list_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_media_list_type" ( :names
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_media_list_type"
						SET :names
							"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mmedlity."id" AS "media.lists.type.id", mmedlity."siteid" AS "media.lists.type.siteid",
							mmedlity."code" AS "media.lists.type.code", mmedlity."domain" AS "media.lists.type.domain",
							mmedlity."label" AS "media.lists.type.label", mmedlity."status" AS "media.lists.type.status",
							mmedlity."mtime" AS "media.lists.type.mtime", mmedlity."editor" AS "media.lists.type.editor",
							mmedlity."ctime" AS "media.lists.type.ctime", mmedlity."pos" AS "media.lists.type.position"
						FROM "mshop_media_list_type" AS mmedlity
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mmedlity."id" AS "media.lists.type.id", mmedlity."siteid" AS "media.lists.type.siteid",
							mmedlity."code" AS "media.lists.type.code", mmedlity."domain" AS "media.lists.type.domain",
							mmedlity."label" AS "media.lists.type.label", mmedlity."status" AS "media.lists.type.status",
							mmedlity."mtime" AS "media.lists.type.mtime", mmedlity."editor" AS "media.lists.type.editor",
							mmedlity."ctime" AS "media.lists.type.ctime", mmedlity."pos" AS "media.lists.type.position"
						FROM "mshop_media_list_type" AS mmedlity
						:joins
						WHERE :cond
						ORDER BY :order
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM(
							SELECT mmedlity."id"
							FROM "mshop_media_list_type" AS mmedlity
							:joins
							WHERE :cond
							ORDER BY mmedlity."id"
							OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM(
							SELECT mmedlity."id"
							FROM "mshop_media_list_type" AS mmedlity
							:joins
							WHERE :cond
							ORDER BY mmedlity."id"
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_media_list_type_seq.CURRVAL FROM DUAL',
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
						FROM "mshop_media_list" AS mmedli
						:joins
						WHERE :cond
						GROUP BY :cols, mmedli."id"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY :keys
				',
				'mysql' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :val AS "val"
						FROM "mshop_media_list" AS mmedli
						:joins
						WHERE :cond
						GROUP BY :cols, mmedli."id"
						ORDER BY :order
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_media_list"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_media_list" ( :names
						"parentid", "key", "type", "domain", "refid", "start", "end",
						"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_media_list"
					SET :names
						"parentid"=?, "key" = ?, "type" = ?, "domain" = ?, "refid" = ?, "start" = ?,
						"end" = ?, "config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mmedli."id" AS "media.lists.id", mmedli."parentid" AS "media.lists.parentid",
						mmedli."siteid" AS "media.lists.siteid", mmedli."type" AS "media.lists.type",
						mmedli."domain" AS "media.lists.domain", mmedli."refid" AS "media.lists.refid",
						mmedli."start" AS "media.lists.datestart", mmedli."end" AS "media.lists.dateend",
						mmedli."config" AS "media.lists.config", mmedli."pos" AS "media.lists.position",
						mmedli."status" AS "media.lists.status", mmedli."mtime" AS "media.lists.mtime",
						mmedli."editor" AS "media.lists.editor", mmedli."ctime" AS "media.lists.ctime"
					FROM "mshop_media_list" AS mmedli
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mmedli."id" AS "media.lists.id", mmedli."parentid" AS "media.lists.parentid",
						mmedli."siteid" AS "media.lists.siteid", mmedli."type" AS "media.lists.type",
						mmedli."domain" AS "media.lists.domain", mmedli."refid" AS "media.lists.refid",
						mmedli."start" AS "media.lists.datestart", mmedli."end" AS "media.lists.dateend",
						mmedli."config" AS "media.lists.config", mmedli."pos" AS "media.lists.position",
						mmedli."status" AS "media.lists.status", mmedli."mtime" AS "media.lists.mtime",
						mmedli."editor" AS "media.lists.editor", mmedli."ctime" AS "media.lists.ctime"
					FROM "mshop_media_list" AS mmedli
					:joins
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM(
						SELECT mmedli."id"
						FROM "mshop_media_list" AS mmedli
						:joins
						WHERE :cond
						ORDER BY mmedli."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM(
						SELECT mmedli."id"
						FROM "mshop_media_list" AS mmedli
						:joins
						WHERE :cond
						ORDER BY mmedli."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_media_list_seq.CURRVAL FROM DUAL',
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
						DELETE FROM "mshop_media_property_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_media_property_type" ( :names
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_media_property_type"
						SET :names
							"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mmedprty."id" AS "media.property.type.id", mmedprty."siteid" AS "media.property.type.siteid",
							mmedprty."code" AS "media.property.type.code", mmedprty."domain" AS "media.property.type.domain",
							mmedprty."label" AS "media.property.type.label", mmedprty."status" AS "media.property.type.status",
							mmedprty."mtime" AS "media.property.type.mtime", mmedprty."editor" AS "media.property.type.editor",
							mmedprty."ctime" AS "media.property.type.ctime", mmedprty."pos" AS "media.property.type.position"
						FROM "mshop_media_property_type" mmedprty
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mmedprty."id" AS "media.property.type.id", mmedprty."siteid" AS "media.property.type.siteid",
							mmedprty."code" AS "media.property.type.code", mmedprty."domain" AS "media.property.type.domain",
							mmedprty."label" AS "media.property.type.label", mmedprty."status" AS "media.property.type.status",
							mmedprty."mtime" AS "media.property.type.mtime", mmedprty."editor" AS "media.property.type.editor",
							mmedprty."ctime" AS "media.property.type.ctime", mmedprty."pos" AS "media.property.type.position"
						FROM "mshop_media_property_type" mmedprty
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
							SELECT mmedprty."id"
							FROM "mshop_media_property_type" mmedprty
							:joins
							WHERE :cond
							ORDER BY mmedprty."id"
							OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mmedprty."id"
							FROM "mshop_media_property_type" mmedprty
							:joins
							WHERE :cond
							ORDER BY mmedprty."id"
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_media_property_type_seq.CURRVAL FROM DUAL',
					'pgsql' => 'SELECT lastval()',
					'sqlite' => 'SELECT last_insert_rowid()',
					'sqlsrv' => 'SELECT @@IDENTITY',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_media_property"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_media_property" ( :names
						"parentid", "key", "type", "langid", "value",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_media_property"
					SET :names
						"parentid" = ?, "key" = ?, "type" = ?, "langid" = ?,
						"value" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mmedpr."id" AS "media.property.id", mmedpr."parentid" AS "media.property.parentid",
						mmedpr."siteid" AS "media.property.siteid", mmedpr."type" AS "media.property.type",
						mmedpr."langid" AS "media.property.languageid", mmedpr."value" AS "media.property.value",
						mmedpr."mtime" AS "media.property.mtime", mmedpr."editor" AS "media.property.editor",
						mmedpr."ctime" AS "media.property.ctime"
					FROM "mshop_media_property" AS mmedpr
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mmedpr."id" AS "media.property.id", mmedpr."parentid" AS "media.property.parentid",
						mmedpr."siteid" AS "media.property.siteid", mmedpr."type" AS "media.property.type",
						mmedpr."langid" AS "media.property.languageid", mmedpr."value" AS "media.property.value",
						mmedpr."mtime" AS "media.property.mtime", mmedpr."editor" AS "media.property.editor",
						mmedpr."ctime" AS "media.property.ctime"
					FROM "mshop_media_property" AS mmedpr
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
						SELECT mmedpr."id"
						FROM "mshop_media_property" AS mmedpr
						:joins
						WHERE :cond
						ORDER BY mmedpr."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mmedpr."id"
						FROM "mshop_media_property" AS mmedpr
						:joins
						WHERE :cond
						ORDER BY mmedpr."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_media_property_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'type' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_media_type"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_media_type" ( :names
						"code", "domain", "label", "pos", "status",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_media_type"
					SET :names
						"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
						"status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mmedty."id" AS "media.type.id", mmedty."siteid" AS "media.type.siteid",
						mmedty."code" AS "media.type.code", mmedty."domain" AS "media.type.domain",
						mmedty."label" AS "media.type.label", mmedty."status" AS "media.type.status",
						mmedty."mtime" AS "media.type.mtime", mmedty."editor" AS "media.type.editor",
						mmedty."ctime" AS "media.type.ctime", mmedty."pos" AS "media.type.position"
					FROM "mshop_media_type" mmedty
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mmedty."id" AS "media.type.id", mmedty."siteid" AS "media.type.siteid",
						mmedty."code" AS "media.type.code", mmedty."domain" AS "media.type.domain",
						mmedty."label" AS "media.type.label", mmedty."status" AS "media.type.status",
						mmedty."mtime" AS "media.type.mtime", mmedty."editor" AS "media.type.editor",
						mmedty."ctime" AS "media.type.ctime", mmedty."pos" AS "media.type.position"
					FROM "mshop_media_type" mmedty
					:joins
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM(
						SELECT mmedty."id"
						FROM "mshop_media_type" mmedty
						:joins
						WHERE :cond
						ORDER BY mmedty."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM(
						SELECT mmedty."id"
						FROM "mshop_media_type" mmedty
						:joins
						WHERE :cond
						ORDER BY mmedty."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_media_type_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_media"
				WHERE :cond AND siteid = ?
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_media" ( :names
					"langid", "type", "label", "mimetype", "link", "status",
					"domain", "preview", "mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_media"
				SET :names
					"langid" = ?, "type" = ?, "label" = ?, "mimetype" = ?, "link" = ?,
					"status" = ?, "domain" = ?, "preview" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
					mmed."id" AS "media.id", mmed."siteid" AS "media.siteid",
					mmed."langid" AS "media.languageid", mmed."type" AS "media.type",
					mmed."link" AS "media.url", mmed."label" AS "media.label",
					mmed."status" AS "media.status", mmed."mimetype" AS "media.mimetype",
					mmed."domain" AS "media.domain", mmed."preview" AS "media.previews",
					mmed."mtime" AS "media.mtime", mmed."editor" AS "media.editor",
					mmed."ctime" AS "media.ctime"
				FROM "mshop_media" AS mmed
				:joins
				WHERE :cond
				GROUP BY :columns :group
					mmed."id", mmed."siteid", mmed."langid", mmed."type", mmed."link",
					mmed."label", mmed."status", mmed."mimetype", mmed."domain", mmed."preview",
					mmed."mtime", mmed."editor", mmed."ctime"
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					mmed."id" AS "media.id", mmed."siteid" AS "media.siteid",
					mmed."langid" AS "media.languageid", mmed."type" AS "media.type",
					mmed."link" AS "media.url", mmed."label" AS "media.label",
					mmed."status" AS "media.status", mmed."mimetype" AS "media.mimetype",
					mmed."domain" AS "media.domain", mmed."preview" AS "media.previews",
					mmed."mtime" AS "media.mtime", mmed."editor" AS "media.editor",
					mmed."ctime" AS "media.ctime"
				FROM "mshop_media" AS mmed
				:joins
				WHERE :cond
				GROUP BY :group mmed."id"
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mmed."id"
					FROM "mshop_media" AS mmed
					:joins
					WHERE :cond
					GROUP BY mmed."id"
					ORDER BY mmed."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mmed."id"
					FROM "mshop_media" AS mmed
					:joins
					WHERE :cond
					GROUP BY mmed."id"
					ORDER BY mmed."id"
					LIMIT 10000 OFFSET 0
				) AS list
			'
		),
		'newid' => array(
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT mshop_media_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
