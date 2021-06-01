<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


return array(
	'manager' => array(
		'address' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_supplier_address"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_supplier_address" ( :names
						"parentid", "company", "vatid", "salutation", "title",
						"firstname", "lastname", "address1", "address2", "address3",
						"postal", "city", "state", "countryid", "langid", "telephone",
						"email", "telefax", "website", "longitude", "latitude",
						"pos", "birthday", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_supplier_address"
					SET :names
						"parentid" = ?, "company" = ?, "vatid" = ?, "salutation" = ?,
						"title" = ?, "firstname" = ?, "lastname" = ?, "address1" = ?,
						"address2" = ?, "address3" = ?, "postal" = ?, "city" = ?,
						"state" = ?, "countryid" = ?, "langid" = ?, "telephone" = ?,
						"email" = ?, "telefax" = ?, "website" = ?, "longitude" = ?, "latitude" = ?,
						"pos" = ?, "birthday" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						msupad."id" AS "supplier.address.id", msupad."siteid" AS "supplier.address.siteid",
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
						msupad."mtime" AS "supplier.address.mtime", msupad."ctime" AS "supplier.address.ctime",
						msupad."editor" AS "supplier.address.editor", msupad."birthday" AS "supplier.address.birthday"
					FROM "mshop_supplier_address" AS msupad
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						msupad."id" AS "supplier.address.id", msupad."siteid" AS "supplier.address.siteid",
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
						msupad."mtime" AS "supplier.address.mtime", msupad."ctime" AS "supplier.address.ctime",
						msupad."editor" AS "supplier.address.editor", msupad."birthday" AS "supplier.address.birthday"
					FROM "mshop_supplier_address" AS msupad
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
						SELECT msupad."id"
						FROM "mshop_supplier_address" AS msupad
						:joins
						WHERE :cond
						ORDER BY msupad."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT msupad."id"
						FROM "mshop_supplier_address" AS msupad
						:joins
						WHERE :cond
						ORDER BY msupad."id"
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
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'lists' => array(
			'type' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_supplier_list_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_supplier_list_type" ( :names
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_supplier_list_type"
						SET :names
							"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							msuplity."id" AS "supplier.lists.type.id", msuplity."siteid" AS "supplier.lists.type.siteid",
							msuplity."code" AS "supplier.lists.type.code", msuplity."domain" AS "supplier.lists.type.domain",
							msuplity."label" AS "supplier.lists.type.label", msuplity."status" AS "supplier.lists.type.status",
							msuplity."mtime" AS "supplier.lists.type.mtime", msuplity."editor" AS "supplier.lists.type.editor",
							msuplity."ctime" AS "supplier.lists.type.ctime", msuplity."pos" AS "supplier.lists.type.position"
						FROM "mshop_supplier_list_type" AS msuplity
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							msuplity."id" AS "supplier.lists.type.id", msuplity."siteid" AS "supplier.lists.type.siteid",
							msuplity."code" AS "supplier.lists.type.code", msuplity."domain" AS "supplier.lists.type.domain",
							msuplity."label" AS "supplier.lists.type.label", msuplity."status" AS "supplier.lists.type.status",
							msuplity."mtime" AS "supplier.lists.type.mtime", msuplity."editor" AS "supplier.lists.type.editor",
							msuplity."ctime" AS "supplier.lists.type.ctime", msuplity."pos" AS "supplier.lists.type.position"
						FROM "mshop_supplier_list_type" AS msuplity
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
							SELECT msuplity."id"
							FROM "mshop_supplier_list_type" AS msuplity
							:joins
							WHERE :cond
							ORDER BY msuplity."id"
							OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT msuplity."id"
							FROM "mshop_supplier_list_type" AS msuplity
							:joins
							WHERE :cond
							ORDER BY msuplity."id"
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
					'sqlsrv' => 'SELECT @@IDENTITY',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
			'aggregate' => array(
				'ansi' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :val AS "val"
						FROM "mshop_supplier_list" AS msupli
						:joins
						WHERE :cond
						GROUP BY :cols, msupli."id"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY :keys
				',
				'mysql' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :val AS "val"
						FROM "mshop_supplier_list" AS msupli
						:joins
						WHERE :cond
						GROUP BY :cols, msupli."id"
						ORDER BY :order
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_supplier_list"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_supplier_list" ( :names
						"parentid", "key", "type", "domain", "refid", "start", "end",
						"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_supplier_list"
					SET :names
						"parentid" = ?, "key" = ?, "type" = ?, "domain" = ?, "refid" = ?, "start" = ?,
						"end" = ?, "config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						msupli."id" AS "supplier.lists.id", msupli."parentid" AS "supplier.lists.parentid",
						msupli."siteid" AS "supplier.lists.siteid", msupli."type" AS "supplier.lists.type",
						msupli."domain" AS "supplier.lists.domain", msupli."refid" AS "supplier.lists.refid",
						msupli."start" AS "supplier.lists.datestart", msupli."end" AS "supplier.lists.dateend",
						msupli."config" AS "supplier.lists.config", msupli."pos" AS "supplier.lists.position",
						msupli."status" AS "supplier.lists.status", msupli."mtime" AS "supplier.lists.mtime",
						msupli."editor" AS "supplier.lists.editor", msupli."ctime" AS "supplier.lists.ctime"
					FROM "mshop_supplier_list" AS msupli
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						msupli."id" AS "supplier.lists.id", msupli."parentid" AS "supplier.lists.parentid",
						msupli."siteid" AS "supplier.lists.siteid", msupli."type" AS "supplier.lists.type",
						msupli."domain" AS "supplier.lists.domain", msupli."refid" AS "supplier.lists.refid",
						msupli."start" AS "supplier.lists.datestart", msupli."end" AS "supplier.lists.dateend",
						msupli."config" AS "supplier.lists.config", msupli."pos" AS "supplier.lists.position",
						msupli."status" AS "supplier.lists.status", msupli."mtime" AS "supplier.lists.mtime",
						msupli."editor" AS "supplier.lists.editor", msupli."ctime" AS "supplier.lists.ctime"
					FROM "mshop_supplier_list" AS msupli
					USE INDEX (unq_mssupli_pid_dm_sid_ty_rid, idx_mssupli_pid_dm_sid_pos_rid, idx_mssupli_rid_dom_sid_ty, idx_mssupli_key_sid)
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
						SELECT msupli."id"
						FROM "mshop_supplier_list" AS msupli
						:joins
						WHERE :cond
						ORDER BY msupli."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT msupli."id"
						FROM "mshop_supplier_list" AS msupli
						:joins
						WHERE :cond
						ORDER BY msupli."id"
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
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_supplier"
				WHERE :cond AND siteid = ?
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_supplier" ( :names
					"code", "label", "status", "mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_supplier"
				SET :names
					"code" = ?, "label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
					msup."id" AS "supplier.id", msup."siteid" AS "supplier.siteid",
					msup."code" AS "supplier.code", msup."label" AS "supplier.label",
					msup."status" AS "supplier.status", msup."mtime" AS "supplier.mtime",
					msup."editor" AS "supplier.editor", msup."ctime" AS "supplier.ctime"
				FROM "mshop_supplier" AS msup
				:joins
				WHERE :cond
				GROUP BY :columns :group
					msup."id", msup."siteid", msup."code", msup."label", msup."status", msup."mtime",
					msup."editor", msup."ctime"
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					msup."id" AS "supplier.id", msup."siteid" AS "supplier.siteid",
					msup."code" AS "supplier.code", msup."label" AS "supplier.label",
					msup."status" AS "supplier.status", msup."mtime" AS "supplier.mtime",
					msup."editor" AS "supplier.editor", msup."ctime" AS "supplier.ctime"
				FROM "mshop_supplier" AS msup
				:joins
				WHERE :cond
				GROUP BY :group msup."id"
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT msup."id"
					FROM "mshop_supplier" AS msup
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
					FROM "mshop_supplier" AS msup
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
