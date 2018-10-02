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
							DELETE FROM "mshop_media_list_type"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_media_list_type" (
								"code", "domain", "label", "pos", "status",
								"mtime", "editor", "siteid", "ctime"
							) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_media_list_type"
							SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
								"status" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT mmedlity."id" AS "media.lists.type.id", mmedlity."siteid" AS "media.lists.type.siteid",
								mmedlity."code" AS "media.lists.type.code", mmedlity."domain" AS "media.lists.type.domain",
								mmedlity."label" AS "media.lists.type.label", mmedlity."status" AS "media.lists.type.status",
								mmedlity."mtime" AS "media.lists.type.mtime", mmedlity."editor" AS "media.lists.type.editor",
								mmedlity."ctime" AS "media.lists.type.ctime", mmedlity."pos" AS "media.lists.type.position"
							FROM "mshop_media_list_type" AS mmedlity
							:joins
							WHERE :cond
							GROUP BY mmedlity."id", mmedlity."siteid", mmedlity."code", mmedlity."domain",
								mmedlity."label", mmedlity."status", mmedlity."mtime", mmedlity."editor",
								mmedlity."ctime", mmedlity."pos" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						'
					),
					'count' => array(
						'ansi' => '
							SELECT COUNT(*) AS "count"
							FROM(
								SELECT DISTINCT mmedlity."id"
								FROM "mshop_media_list_type" AS mmedlity
								:joins
								WHERE :cond
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
							SELECT :key AS "key", mmedli."id" AS "id"
							FROM "mshop_media_list" AS mmedli
							:joins
							WHERE :cond
							GROUP BY :key, mmedli."id" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY "key"
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_media_list"
						WHERE :cond AND siteid = ?
					'
				),
				'getposmax' => array(
					'ansi' => '
						SELECT MAX( "pos" ) AS pos
						FROM "mshop_media_list"
						WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
							AND "domain" = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_media_list"(
							"parentid", "typeid", "domain", "refid", "start", "end",
							"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_media_list"
						SET "parentid"=?, "typeid" = ?, "domain" = ?, "refid" = ?, "start" = ?, "end" = ?,
							"config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'updatepos' => array(
					'ansi' => '
						UPDATE "mshop_media_list"
							SET "pos" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'move' => array(
					'ansi' => '
						UPDATE "mshop_media_list"
							SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
							AND "domain" = ? AND "pos" >= ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mmedli."id" AS "media.lists.id", mmedli."parentid" AS "media.lists.parentid",
							mmedli."siteid" AS "media.lists.siteid", mmedli."typeid" AS "media.lists.typeid",
							mmedli."domain" AS "media.lists.domain", mmedli."refid" AS "media.lists.refid",
							mmedli."start" AS "media.lists.datestart", mmedli."end" AS "media.lists.dateend",
							mmedli."config" AS "media.lists.config", mmedli."pos" AS "media.lists.position",
							mmedli."status" AS "media.lists.status", mmedli."mtime" AS "media.lists.mtime",
							mmedli."editor" AS "media.lists.editor", mmedli."ctime" AS "media.lists.ctime"
						FROM "mshop_media_list" AS mmedli
						:joins
						WHERE :cond
						GROUP BY mmedli."id", mmedli."parentid", mmedli."siteid", mmedli."typeid",
							mmedli."domain", mmedli."refid", mmedli."start", mmedli."end",
							mmedli."config", mmedli."pos", mmedli."status", mmedli."mtime",
							mmedli."editor", mmedli."ctime" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM(
							SELECT DISTINCT mmedli."id"
							FROM "mshop_media_list" AS mmedli
							:joins
							WHERE :cond
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
							DELETE FROM "mshop_media_property_type"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_media_property_type" (
								"code", "domain", "label", "pos", "status",
								"mtime", "editor", "siteid", "ctime"
							) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_media_property_type"
							SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
								"status" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT mmedprty."id" AS "media.property.type.id", mmedprty."siteid" AS "media.property.type.siteid",
								mmedprty."code" AS "media.property.type.code", mmedprty."domain" AS "media.property.type.domain",
								mmedprty."label" AS "media.property.type.label", mmedprty."status" AS "media.property.type.status",
								mmedprty."mtime" AS "media.property.type.mtime", mmedprty."editor" AS "media.property.type.editor",
								mmedprty."ctime" AS "media.property.type.ctime", mmedprty."pos" AS "media.property.type.position"
							FROM "mshop_media_property_type" mmedprty
							:joins
							WHERE :cond
							GROUP BY mmedprty."id", mmedprty."siteid", mmedprty."code", mmedprty."domain",
								mmedprty."label", mmedprty."status", mmedprty."mtime", mmedprty."editor",
								mmedprty."ctime", mmedprty."pos" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						'
					),
					'count' => array(
						'ansi' => '
							SELECT COUNT(*) AS "count"
							FROM (
								SELECT DISTINCT mmedprty."id"
								FROM "mshop_media_property_type" mmedprty
								:joins
								WHERE :cond
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
						'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
						'sqlanywhere' => 'SELECT @@IDENTITY',
					),
				),
			),
			'standard' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_media_property"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_media_property" (
							"parentid", "typeid", "langid", "value",
							"mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_media_property"
						SET "parentid" = ?, "typeid" = ?, "langid" = ?,
							"value" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mmedpr."id" AS "media.property.id", mmedpr."parentid" AS "media.property.parentid",
							mmedpr."siteid" AS "media.property.siteid", mmedpr."typeid" AS "media.property.typeid",
							mmedpr."langid" AS "media.property.languageid", mmedpr."value" AS "media.property.value",
							mmedpr."mtime" AS "media.property.mtime", mmedpr."editor" AS "media.property.editor",
							mmedpr."ctime" AS "media.property.ctime"
						FROM "mshop_media_property" AS mmedpr
						:joins
						WHERE :cond
						GROUP BY mmedpr."id", mmedpr."parentid", mmedpr."siteid", mmedpr."typeid",
							mmedpr."langid", mmedpr."value", mmedpr."mtime", mmedpr."editor",
							mmedpr."ctime" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mmedpr."id"
							FROM "mshop_media_property" AS mmedpr
							:joins
							WHERE :cond
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
					'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
		),
		'type' => array(
			'standard' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_media_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_media_type" (
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_media_type"
						SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mmedty."id" AS "media.type.id", mmedty."siteid" AS "media.type.siteid",
							mmedty."code" AS "media.type.code", mmedty."domain" AS "media.type.domain",
							mmedty."label" AS "media.type.label", mmedty."status" AS "media.type.status",
							mmedty."mtime" AS "media.type.mtime", mmedty."editor" AS "media.type.editor",
							mmedty."ctime" AS "media.type.ctime", mmedty."pos" AS "media.type.position"
						FROM "mshop_media_type" mmedty
						:joins
						WHERE :cond
						GROUP BY mmedty."id", mmedty."siteid", mmedty."code", mmedty."domain",
							mmedty."label", mmedty."status", mmedty."mtime", mmedty."editor",
							mmedty."ctime", mmedty."pos" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM(
							SELECT DISTINCT mmedty."id"
							FROM "mshop_media_type" mmedty
							:joins
							WHERE :cond
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
					'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
		),
		'standard' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_media"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_media" (
						"langid", "typeid", "label", "mimetype", "link", "status",
						"domain", "preview", "mtime", "editor", "siteid", "ctime"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_media"
					SET "langid" = ?, "typeid" = ?, "label" = ?, "mimetype" = ?, "link" = ?,
						"status" = ?, "domain" = ?, "preview" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT mmed."id" AS "media.id", mmed."siteid" AS "media.siteid",
						mmed."langid" AS "media.languageid", mmed."typeid" AS "media.typeid",
						mmed."link" AS "media.url", mmed."label" AS "media.label",
						mmed."status" AS "media.status", mmed."mimetype" AS "media.mimetype",
						mmed."domain" AS "media.domain", mmed."preview" AS "media.preview",
						mmed."mtime" AS "media.mtime", mmed."editor" AS "media.editor",
						mmed."ctime" AS "media.ctime"
					FROM "mshop_media" AS mmed
					:joins
					WHERE :cond
					GROUP BY mmed."id", mmed."siteid", mmed."langid", mmed."typeid",
						mmed."link", mmed."label", mmed."status", mmed."mimetype",
						mmed."domain", mmed."preview", mmed."mtime", mmed."editor",
						mmed."ctime" /*-columns*/ , :columns /*columns-*/
					/*-orderby*/ ORDER BY :order /*orderby-*/
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT DISTINCT mmed."id"
						FROM "mshop_media" AS mmed
						:joins
						WHERE :cond
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
				'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
	),
);