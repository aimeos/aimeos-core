<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


return array(
	'manager' => array(
		'address' => array(
			'standard' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_supplier_address"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_supplier_address" (
							"parentid", "company", "vatid", "salutation", "title",
							"firstname", "lastname", "address1", "address2", "address3",
							"postal", "city", "state", "countryid", "langid", "telephone",
							"email", "telefax", "website", "longitude", "latitude",
							"flag", "pos", "mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_supplier_address"
						SET "parentid" = ?, "company" = ?, "vatid" = ?, "salutation" = ?,
							"title" = ?, "firstname" = ?, "lastname" = ?, "address1" = ?,
							"address2" = ?, "address3" = ?, "postal" = ?, "city" = ?,
							"state" = ?, "countryid" = ?, "langid" = ?, "telephone" = ?,
							"email" = ?, "telefax" = ?, "website" = ?, "longitude" = ?, "latitude" = ?,
							"flag" = ?, "pos" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT msupad."id" AS "supplier.address.id", msupad."siteid" AS "supplier.address.siteid",
							msupad."parentid" AS "supplier.address.parentid", msupad."pos" AS "supplier.address.position",
							msupad."company" AS "supplier.address.company", msupad."vatid" AS "supplier.address.vatid",
							msupad."salutation" AS "supplier.address.salutation", msupad."title" AS "supplier.address.title",
							msupad."firstname" AS "supplier.address.firstname", msupad."lastname" AS "supplier.address.lastname",
							msupad."address1" AS "supplier.address.address1", msupad."address2" AS "supplier.address.address2",
							msupad."address3" AS "supplier.address.address3", msupad."postal" AS "supplier.address.postal",
							msupad."city" AS "supplier.address.city", msupad."state" AS "supplier.address.state",
							msupad."countryid" AS "supplier.address.countryid", msupad."langid" AS "supplier.address.languageid",
							msupad."telephone" AS "supplier.address.telephone", msupad."email" AS "supplier.address.email",
							msupad."telefax" AS "supplier.address.telefax", msupad."website" AS "supplier.address.website",
							msupad."longitude" AS "supplier.address.longitude", msupad."latitude" AS "supplier.address.latitude",
							msupad."flag" AS "supplier.address.flag", msupad."mtime" AS "supplier.address.mtime",
							msupad."editor" AS "supplier.address.editor", msupad."ctime" AS "supplier.address.ctime"
						FROM "mshop_supplier_address" AS msupad
						:joins
						WHERE :cond
						GROUP BY msupad."id", msupad."siteid", msupad."parentid", msupad."pos",
							msupad."company", msupad."vatid", msupad."salutation", msupad."title",
							msupad."firstname", msupad."lastname", msupad."address1", msupad."address2",
							msupad."address3", msupad."postal", msupad."city", msupad."state",
							msupad."countryid", msupad."langid", msupad."telephone", msupad."email",
							msupad."telefax", msupad."website", msupad."longitude", msupad."latitude",
							msupad."flag", msupad."mtime", msupad."editor", msupad."ctime"
							/*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT msupad."id"
							FROM "mshop_supplier_address" AS msupad
							:joins
							WHERE :cond
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_supplier_address_seq.CURRVAL FROM DUAL',
					'pgsql' => 'SELECT lastval()',
					'sqlite' => 'SELECT last_insert_rowid()',
					'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
		),
		'lists' => array(
			'type' => array(
				'standard' => array(
					'delete' => array(
						'ansi' => '
							DELETE FROM "mshop_supplier_list_type"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_supplier_list_type" (
								"code", "domain", "label", "pos", "status",
								"mtime", "editor", "siteid", "ctime"
							) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_supplier_list_type"
							SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
								"status" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT msuplity."id" AS "supplier.lists.type.id", msuplity."siteid" AS "supplier.lists.type.siteid",
								msuplity."code" AS "supplier.lists.type.code", msuplity."domain" AS "supplier.lists.type.domain",
								msuplity."label" AS "supplier.lists.type.label", msuplity."status" AS "supplier.lists.type.status",
								msuplity."mtime" AS "supplier.lists.type.mtime", msuplity."editor" AS "supplier.lists.type.editor",
								msuplity."ctime" AS "supplier.lists.type.ctime", msuplity."pos" AS "supplier.lists.type.position"
							FROM "mshop_supplier_list_type" AS msuplity
							:joins
							WHERE :cond
							GROUP BY msuplity."id", msuplity."siteid", msuplity."code", msuplity."domain",
								msuplity."label", msuplity."status", msuplity."mtime", msuplity."editor",
								msuplity."ctime", msuplity."pos" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						'
					),
					'count' => array(
						'ansi' => '
							SELECT COUNT(*) AS "count"
							FROM (
								SELECT DISTINCT msuplity."id"
								FROM "mshop_supplier_list_type" AS msuplity
								:joins
								WHERE :cond
								LIMIT 10000 OFFSET 0
							) AS list
						'
					),
					'newid' => array(
						'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
						'mysql' => 'SELECT LAST_INSERT_ID()',
						'oracle' => 'SELECT mshop_supplier_list_type_seq.CURRVAL FROM DUAL',
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
							SELECT :key AS "key", msupli."id" AS "id"
							FROM "mshop_supplier_list" AS msupli
							:joins
							WHERE :cond
							GROUP BY :key, msupli."id" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY "key"
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_supplier_list"
						WHERE :cond AND siteid = ?
					'
				),
				'getposmax' => array(
					'ansi' => '
						SELECT MAX( "pos" ) AS pos
						FROM "mshop_supplier_list"
						WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
							AND "domain" = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_supplier_list" (
							"parentid", "typeid", "domain", "refid", "start", "end",
							"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_supplier_list"
						SET "parentid" = ?, "typeid" = ?, "domain" = ?, "refid" = ?, "start" = ?, "end" = ?,
							"config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'updatepos' => array(
					'ansi' => '
						UPDATE "mshop_supplier_list"
						SET "pos" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'move' => array(
					'ansi' => '
						UPDATE "mshop_supplier_list"
						SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
							AND "domain" = ? AND "pos" >= ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT msupli."id" AS "supplier.lists.id", msupli."parentid" AS "supplier.lists.parentid",
							msupli."siteid" AS "supplier.lists.siteid", msupli."typeid" AS "supplier.lists.typeid",
							msupli."domain" AS "supplier.lists.domain", msupli."refid" AS "supplier.lists.refid",
							msupli."start" AS "supplier.lists.datestart", msupli."end" AS "supplier.lists.dateend",
							msupli."config" AS "supplier.lists.config", msupli."pos" AS "supplier.lists.position",
							msupli."status" AS "supplier.lists.status", msupli."mtime" AS "supplier.lists.mtime",
							msupli."editor" AS "supplier.lists.editor", msupli."ctime" AS "supplier.lists.ctime"
						FROM "mshop_supplier_list" AS msupli
						:joins
						WHERE :cond
						GROUP BY msupli."id", msupli."parentid", msupli."siteid", msupli."typeid",
							msupli."domain", msupli."refid", msupli."start", msupli."end",
							msupli."config", msupli."pos", msupli."status", msupli."mtime",
							msupli."editor", msupli."ctime" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT msupli."id"
							FROM "mshop_supplier_list" AS msupli
							:joins
							WHERE :cond
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_supplier_list_seq.CURRVAL FROM DUAL',
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
					DELETE FROM "mshop_supplier"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_supplier" (
						"code", "label", "status", "mtime", "editor", "siteid", "ctime"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_supplier"
					SET "code" = ?, "label" = ?, "status" = ?,
						"mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT msup."id" AS "supplier.id", msup."siteid" AS "supplier.siteid",
						msup."code" AS "supplier.code", msup."label" AS "supplier.label",
						msup."status" AS "supplier.status", msup."mtime" AS "supplier.mtime",
						msup."editor" AS "supplier.editor", msup."ctime" AS "supplier.ctime"
					FROM "mshop_supplier" AS msup
					:joins
					WHERE :cond
					GROUP BY msup."id", msup."siteid", msup."code", msup."label",
						msup."status", msup."mtime", msup."editor", msup."ctime"
						/*-columns*/ , :columns /*columns-*/
					/*-orderby*/ ORDER BY :order /*orderby-*/
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT DISTINCT msup."id"
						FROM "mshop_supplier" AS msup
						:joins
						WHERE :cond
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
				'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
	),
);