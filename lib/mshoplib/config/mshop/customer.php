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
					DELETE FROM "mshop_customer_address"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_customer_address" ( :names
						"parentid", "company", "vatid", "salutation", "title",
						"firstname", "lastname", "address1", "address2", "address3",
						"postal", "city", "state", "countryid", "langid", "telephone",
						"email", "telefax", "website", "longitude", "latitude", "pos",
						"birthday", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_customer_address"
					SET :names
						"parentid" = ?, "company" = ?, "vatid" = ?, "salutation" = ?,
						"title" = ?, "firstname" = ?, "lastname" = ?, "address1" = ?,
						"address2" = ?, "address3" = ?, "postal" = ?, "city" = ?,
						"state" = ?, "countryid" = ?, "langid" = ?, "telephone" = ?,
						"email" = ?, "telefax" = ?, "website" = ?, "longitude" = ?, "latitude" = ?,
						"pos" = ?, "birthday" = ?, "mtime" = ?, "editor" = ?, "siteid" = ?
					WHERE "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mcusad."id" AS "customer.address.id", mcusad."siteid" AS "customer.address.siteid",
						mcusad."parentid" AS "customer.address.parentid", mcusad."pos" AS "customer.address.position",
						mcusad."company" AS "customer.address.company", mcusad."vatid" AS "customer.address.vatid",
						mcusad."salutation" AS "customer.address.salutation", mcusad."title" AS "customer.address.title",
						mcusad."firstname" AS "customer.address.firstname", mcusad."lastname" AS "customer.address.lastname",
						mcusad."address1" AS "customer.address.address1", mcusad."address2" AS "customer.address.address2",
						mcusad."address3" AS "customer.address.address3", mcusad."postal" AS "customer.address.postal",
						mcusad."city" AS "customer.address.city", mcusad."state" AS "customer.address.state",
						mcusad."countryid" AS "customer.address.countryid", mcusad."langid" AS "customer.address.languageid",
						mcusad."telephone" AS "customer.address.telephone", mcusad."email" AS "customer.address.email",
						mcusad."telefax" AS "customer.address.telefax", mcusad."website" AS "customer.address.website",
						mcusad."longitude" AS "customer.address.longitude", mcusad."latitude" AS "customer.address.latitude",
						mcusad."mtime" AS "customer.address.mtime", mcusad."editor" AS "customer.address.editor",
						mcusad."ctime" AS "customer.address.ctime", mcusad."birthday" AS "customer.address.birthday"
					FROM "mshop_customer_address" AS mcusad
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mcusad."id" AS "customer.address.id", mcusad."siteid" AS "customer.address.siteid",
						mcusad."parentid" AS "customer.address.parentid", mcusad."pos" AS "customer.address.position",
						mcusad."company" AS "customer.address.company", mcusad."vatid" AS "customer.address.vatid",
						mcusad."salutation" AS "customer.address.salutation", mcusad."title" AS "customer.address.title",
						mcusad."firstname" AS "customer.address.firstname", mcusad."lastname" AS "customer.address.lastname",
						mcusad."address1" AS "customer.address.address1", mcusad."address2" AS "customer.address.address2",
						mcusad."address3" AS "customer.address.address3", mcusad."postal" AS "customer.address.postal",
						mcusad."city" AS "customer.address.city", mcusad."state" AS "customer.address.state",
						mcusad."countryid" AS "customer.address.countryid", mcusad."langid" AS "customer.address.languageid",
						mcusad."telephone" AS "customer.address.telephone", mcusad."email" AS "customer.address.email",
						mcusad."telefax" AS "customer.address.telefax", mcusad."website" AS "customer.address.website",
						mcusad."longitude" AS "customer.address.longitude", mcusad."latitude" AS "customer.address.latitude",
						mcusad."mtime" AS "customer.address.mtime", mcusad."editor" AS "customer.address.editor",
						mcusad."ctime" AS "customer.address.ctime", mcusad."birthday" AS "customer.address.birthday"
					FROM "mshop_customer_address" AS mcusad
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
						SELECT mcusad."id"
						FROM "mshop_customer_address" AS mcusad
						:joins
						WHERE :cond
						ORDER BY mcusad."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mcusad."id"
						FROM "mshop_customer_address" AS mcusad
						:joins
						WHERE :cond
						ORDER BY mcusad."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_customer_address_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'group' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_customer_group"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_customer_group" ( :names
						"code", "label", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_customer_group"
					SET :names
						"code" = ?, "label" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mcusgr."id" AS "customer.group.id", mcusgr."siteid" AS "customer.group.siteid",
						mcusgr."code" AS "customer.group.code", mcusgr."label" AS "customer.group.label",
						mcusgr."mtime" AS "customer.group.mtime", mcusgr."editor" AS "customer.group.editor",
						mcusgr."ctime" AS "customer.group.ctime"
					FROM "mshop_customer_group" AS mcusgr
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mcusgr."id" AS "customer.group.id", mcusgr."siteid" AS "customer.group.siteid",
						mcusgr."code" AS "customer.group.code", mcusgr."label" AS "customer.group.label",
						mcusgr."mtime" AS "customer.group.mtime", mcusgr."editor" AS "customer.group.editor",
						mcusgr."ctime" AS "customer.group.ctime"
					FROM "mshop_customer_group" AS mcusgr
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
						SELECT mcusgr."id"
						FROM "mshop_customer_group" AS mcusgr
						:joins
						WHERE :cond
						ORDER BY mcusgr."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mcusgr."id"
						FROM "mshop_customer_group" AS mcusgr
						:joins
						WHERE :cond
						ORDER BY mcusgr."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_customer_group_seq.CURRVAL FROM DUAL',
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
						DELETE FROM "mshop_customer_list_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_customer_list_type" ( :names
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_customer_list_type"
						SET :names
							"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mcuslity."id" AS "customer.lists.type.id", mcuslity."siteid" AS "customer.lists.type.siteid",
							mcuslity."code" AS "customer.lists.type.code", mcuslity."domain" AS "customer.lists.type.domain",
							mcuslity."label" AS "customer.lists.type.label", mcuslity."status" AS "customer.lists.type.status",
							mcuslity."mtime" AS "customer.lists.type.mtime", mcuslity."editor" AS "customer.lists.type.editor",
							mcuslity."ctime" AS "customer.lists.type.ctime", mcuslity."pos" AS "customer.lists.type.position"
						FROM "mshop_customer_list_type" AS mcuslity
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mcuslity."id" AS "customer.lists.type.id", mcuslity."siteid" AS "customer.lists.type.siteid",
							mcuslity."code" AS "customer.lists.type.code", mcuslity."domain" AS "customer.lists.type.domain",
							mcuslity."label" AS "customer.lists.type.label", mcuslity."status" AS "customer.lists.type.status",
							mcuslity."mtime" AS "customer.lists.type.mtime", mcuslity."editor" AS "customer.lists.type.editor",
							mcuslity."ctime" AS "customer.lists.type.ctime", mcuslity."pos" AS "customer.lists.type.position"
						FROM "mshop_customer_list_type" AS mcuslity
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
							SELECT mcuslity."id"
							FROM "mshop_customer_list_type" as mcuslity
							:joins
							WHERE :cond
							ORDER BY mcuslity."id"
							OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
						) AS LIST
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mcuslity."id"
							FROM "mshop_customer_list_type" as mcuslity
							:joins
							WHERE :cond
							ORDER BY mcuslity."id"
							LIMIT 10000 OFFSET 0
						) AS LIST
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_customer_list_type_seq.CURRVAL FROM DUAL',
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
						FROM "mshop_customer_list" AS mcusli
						:joins
						WHERE :cond
						GROUP BY :cols, mcusli."id"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY :keys
				',
				'mysql' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :val AS "val"
						FROM "mshop_customer_list" AS mcusli
						:joins
						WHERE :cond
						GROUP BY :cols, mcusli."id"
						ORDER BY :order
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_customer_list"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_customer_list" ( :names
						"parentid", "key", "type", "domain", "refid", "start", "end",
						"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_customer_list"
					SET :names
						"parentid"=?, "key" = ?, "type" = ?, "domain" = ?, "refid" = ?, "start" = ?,
						"end" = ?, "config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mcusli."id" AS "customer.lists.id", mcusli."parentid" AS "customer.lists.parentid",
						mcusli."siteid" AS "customer.lists.siteid", mcusli."type" AS "customer.lists.type",
						mcusli."domain" AS "customer.lists.domain", mcusli."refid" AS "customer.lists.refid",
						mcusli."start" AS "customer.lists.datestart", mcusli."end" AS "customer.lists.dateend",
						mcusli."config" AS "customer.lists.config", mcusli."pos" AS "customer.lists.position",
						mcusli."status" AS "customer.lists.status", mcusli."mtime" AS "customer.lists.mtime",
						mcusli."editor" AS "customer.lists.editor", mcusli."ctime" AS "customer.lists.ctime"
					FROM "mshop_customer_list" AS mcusli
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mcusli."id" AS "customer.lists.id", mcusli."parentid" AS "customer.lists.parentid",
						mcusli."siteid" AS "customer.lists.siteid", mcusli."type" AS "customer.lists.type",
						mcusli."domain" AS "customer.lists.domain", mcusli."refid" AS "customer.lists.refid",
						mcusli."start" AS "customer.lists.datestart", mcusli."end" AS "customer.lists.dateend",
						mcusli."config" AS "customer.lists.config", mcusli."pos" AS "customer.lists.position",
						mcusli."status" AS "customer.lists.status", mcusli."mtime" AS "customer.lists.mtime",
						mcusli."editor" AS "customer.lists.editor", mcusli."ctime" AS "customer.lists.ctime"
					FROM "mshop_customer_list" AS mcusli
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
						SELECT mcusli."id"
						FROM "mshop_customer_list" AS mcusli
						:joins
						WHERE :cond
						ORDER BY mcusli."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mcusli."id"
						FROM "mshop_customer_list" AS mcusli
						:joins
						WHERE :cond
						ORDER BY mcusli."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_customer_list_seq.CURRVAL FROM DUAL',
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
						DELETE FROM "mshop_customer_property_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_customer_property_type" ( :names
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_customer_property_type"
						SET :names
							"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mcusprty."id" AS "customer.property.type.id", mcusprty."siteid" AS "customer.property.type.siteid",
							mcusprty."code" AS "customer.property.type.code", mcusprty."domain" AS "customer.property.type.domain",
							mcusprty."label" AS "customer.property.type.label", mcusprty."status" AS "customer.property.type.status",
							mcusprty."mtime" AS "customer.property.type.mtime", mcusprty."editor" AS "customer.property.type.editor",
							mcusprty."ctime" AS "customer.property.type.ctime", mcusprty."pos" AS "customer.property.type.position"
						FROM "mshop_customer_property_type" mcusprty
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mcusprty."id" AS "customer.property.type.id", mcusprty."siteid" AS "customer.property.type.siteid",
							mcusprty."code" AS "customer.property.type.code", mcusprty."domain" AS "customer.property.type.domain",
							mcusprty."label" AS "customer.property.type.label", mcusprty."status" AS "customer.property.type.status",
							mcusprty."mtime" AS "customer.property.type.mtime", mcusprty."editor" AS "customer.property.type.editor",
							mcusprty."ctime" AS "customer.property.type.ctime", mcusprty."pos" AS "customer.property.type.position"
						FROM "mshop_customer_property_type" mcusprty
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
							SELECT mcusprty."id"
							FROM "mshop_customer_property_type" mcusprty
							:joins
							WHERE :cond
							ORDER BY mcusprty."id"
							OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mcusprty."id"
							FROM "mshop_customer_property_type" mcusprty
							:joins
							WHERE :cond
							ORDER BY mcusprty."id"
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_customer_property_type_seq.CURRVAL FROM DUAL',
					'pgsql' => 'SELECT lastval()',
					'sqlite' => 'SELECT last_insert_rowid()',
					'sqlsrv' => 'SELECT @@IDENTITY',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_customer_property"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_customer_property" ( :names
						"parentid", "key", "type", "langid", "value",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_customer_property"
					SET :names
						"parentid" = ?, "key" = ?, "type" = ?, "langid" = ?,
						"value" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mcuspr."id" AS "customer.property.id", mcuspr."parentid" AS "customer.property.parentid",
						mcuspr."siteid" AS "customer.property.siteid", mcuspr."type" AS "customer.property.type",
						mcuspr."langid" AS "customer.property.languageid", mcuspr."value" AS "customer.property.value",
						mcuspr."mtime" AS "customer.property.mtime", mcuspr."editor" AS "customer.property.editor",
						mcuspr."ctime" AS "customer.property.ctime"
					FROM "mshop_customer_property" AS mcuspr
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mcuspr."id" AS "customer.property.id", mcuspr."parentid" AS "customer.property.parentid",
						mcuspr."siteid" AS "customer.property.siteid", mcuspr."type" AS "customer.property.type",
						mcuspr."langid" AS "customer.property.languageid", mcuspr."value" AS "customer.property.value",
						mcuspr."mtime" AS "customer.property.mtime", mcuspr."editor" AS "customer.property.editor",
						mcuspr."ctime" AS "customer.property.ctime"
					FROM "mshop_customer_property" AS mcuspr
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
						SELECT mcuspr."id"
						FROM "mshop_customer_property" AS mcuspr
						:joins
						WHERE :cond
						ORDER BY mcuspr."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mcuspr."id"
						FROM "mshop_customer_property" AS mcuspr
						:joins
						WHERE :cond
						ORDER BY mcuspr."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_customer_property_seq.CURRVAL FROM DUAL',
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
					FROM "mshop_customer" AS mcus
					:joins
					WHERE :cond
					GROUP BY mcus.id, :cols, :val
					ORDER BY mcus.id DESC
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				) AS list
				GROUP BY :keys
			',
			'mysql' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :val AS "val"
					FROM "mshop_customer" AS mcus
					:joins
					WHERE :cond
					GROUP BY mcus.id, :cols, :val
					ORDER BY mcus.id DESC
					LIMIT :size OFFSET :start
				) AS list
				GROUP BY :keys
			'
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_customer"
				WHERE :cond AND "siteid" = ?
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_customer" ( :names
					"label", "code", "company", "vatid", "salutation", "title",
					"firstname", "lastname", "address1", "address2", "address3",
					"postal", "city", "state", "countryid", "langid", "telephone",
					"email", "telefax", "website", "longitude", "latitude", "birthday",
					"status", "vdate", "password", "mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_customer"
				SET :names
					"label" = ?, "code" = ?, "company" = ?, "vatid" = ?,
					"salutation" = ?, "title" = ?, "firstname" = ?, "lastname" = ?,
					"address1" = ?, "address2" = ?, "address3" = ?, "postal" = ?,
					"city" = ?, "state" = ?, "countryid" = ?, "langid" = ?,
					"telephone" = ?, "email" = ?, "telefax" = ?, "website" = ?,
					"longitude" = ?, "latitude" = ?, "birthday" = ?, "status" = ?,
					"vdate" = ?, "password" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
					mcus."id" AS "customer.id", mcus."siteid" AS "customer.siteid",
					mcus."label" AS "customer.label", mcus."code" AS "customer.code",
					mcus."company" AS "customer.company", mcus."vatid" AS "customer.vatid",
					mcus."salutation" AS "customer.salutation", mcus."title" AS "customer.title",
					mcus."firstname" AS "customer.firstname", mcus."lastname" AS "customer.lastname",
					mcus."address1" AS "customer.address1", mcus."address2" AS "customer.address2",
					mcus."address3" AS "customer.address3", mcus."postal" AS "customer.postal",
					mcus."city" AS "customer.city", mcus."state" AS "customer.state",
					mcus."countryid" AS "customer.countryid", mcus."langid" AS "customer.languageid",
					mcus."telephone" AS "customer.telephone", mcus."email" AS "customer.email",
					mcus."telefax" AS "customer.telefax", mcus."website" AS "customer.website",
					mcus."longitude" AS "customer.longitude", mcus."latitude" AS "customer.latitude",
					mcus."birthday" AS "customer.birthday", mcus."status" AS "customer.status",
					mcus."vdate" AS "customer.dateverified", mcus."password" AS "customer.password",
					mcus."ctime" AS "customer.ctime", mcus."mtime" AS "customer.mtime",
					mcus."editor" AS "customer.editor"
				FROM "mshop_customer" AS mcus
				:joins
				WHERE :cond
				GROUP BY :columns :group
					mcus."id", mcus."siteid", mcus."label", mcus."code", mcus."company", mcus."vatid",
					mcus."salutation", mcus."title", mcus."firstname", mcus."lastname", mcus."address1",
					mcus."address2", mcus."address3", mcus."postal", mcus."city", mcus."state",
					mcus."countryid", mcus."langid", mcus."telephone", mcus."email", mcus."telefax",
					mcus."website", mcus."longitude", mcus."latitude", mcus."birthday", mcus."status",
					mcus."vdate", mcus."password", mcus."ctime", mcus."mtime", mcus."editor"
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					mcus."id" AS "customer.id", mcus."siteid" AS "customer.siteid",
					mcus."label" AS "customer.label", mcus."code" AS "customer.code",
					mcus."company" AS "customer.company", mcus."vatid" AS "customer.vatid",
					mcus."salutation" AS "customer.salutation", mcus."title" AS "customer.title",
					mcus."firstname" AS "customer.firstname", mcus."lastname" AS "customer.lastname",
					mcus."address1" AS "customer.address1", mcus."address2" AS "customer.address2",
					mcus."address3" AS "customer.address3", mcus."postal" AS "customer.postal",
					mcus."city" AS "customer.city", mcus."state" AS "customer.state",
					mcus."countryid" AS "customer.countryid", mcus."langid" AS "customer.languageid",
					mcus."telephone" AS "customer.telephone", mcus."email" AS "customer.email",
					mcus."telefax" AS "customer.telefax", mcus."website" AS "customer.website",
					mcus."longitude" AS "customer.longitude", mcus."latitude" AS "customer.latitude",
					mcus."birthday" AS "customer.birthday", mcus."status" AS "customer.status",
					mcus."vdate" AS "customer.dateverified", mcus."password" AS "customer.password",
					mcus."ctime" AS "customer.ctime", mcus."mtime" AS "customer.mtime",
					mcus."editor" AS "customer.editor"
				FROM "mshop_customer" AS mcus
				:joins
				WHERE :cond
				GROUP BY :group mcus."id"
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mcus."id"
					FROM "mshop_customer" AS mcus
					:joins
					WHERE :cond
					GROUP BY mcus."id"
					ORDER BY mcus."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mcus."id"
					FROM "mshop_customer" AS mcus
					:joins
					WHERE :cond
					GROUP BY mcus."id"
					ORDER BY mcus."id"
					LIMIT 10000 OFFSET 0
				) AS list
			'
		),
		'newid' => array(
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT mshop_customer_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
