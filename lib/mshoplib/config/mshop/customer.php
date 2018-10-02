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
						DELETE FROM "mshop_customer_address"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_customer_address" (
							"parentid", "company", "vatid", "salutation", "title",
							"firstname", "lastname", "address1", "address2", "address3",
							"postal", "city", "state", "countryid", "langid", "telephone",
							"email", "telefax", "website", "longitude", "latitude", "flag",
							"pos", "mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_customer_address"
						SET "parentid" = ?, "company" = ?, "vatid" = ?, "salutation" = ?,
							"title" = ?, "firstname" = ?, "lastname" = ?, "address1" = ?,
							"address2" = ?, "address3" = ?, "postal" = ?, "city" = ?,
							"state" = ?, "countryid" = ?, "langid" = ?, "telephone" = ?,
							"email" = ?, "telefax" = ?, "website" = ?, "longitude" = ?, "latitude" = ?,
							"flag" = ?, "pos" = ?, "mtime" = ?, "editor" = ?, "siteid" = ?
						WHERE "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mcusad."id" AS "customer.address.id", mcusad."siteid" AS "customer.address.siteid",
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
							mcusad."flag" AS "customer.address.flag", mcusad."mtime" AS "customer.address.mtime",
							mcusad."editor" AS "customer.address.editor", mcusad."ctime" AS "customer.address.ctime"
						FROM "mshop_customer_address" AS mcusad
						:joins
						WHERE :cond
						GROUP BY mcusad."id", mcusad."siteid", mcusad."parentid", mcusad."pos",
							mcusad."company", mcusad."vatid", mcusad."salutation", mcusad."title",
							mcusad."firstname", mcusad."lastname", mcusad."address1", mcusad."address2",
							mcusad."address3", mcusad."postal", mcusad."city", mcusad."state",
							mcusad."countryid", mcusad."langid", mcusad."telephone", mcusad."email",
							mcusad."telefax", mcusad."website", mcusad."longitude", mcusad."latitude",
							mcusad."flag", mcusad."mtime", mcusad."editor", mcusad."ctime"
							/*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mcusad."id"
							FROM "mshop_customer_address" AS mcusad
							:joins
							WHERE :cond
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
					'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
		),
		'group' => array(
			'standard' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_customer_group"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_customer_group" (
							"code", "label", "mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_customer_group"
						SET "code" = ?, "label" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mcusgr."id" AS "customer.group.id", mcusgr."siteid" AS "customer.group.siteid",
							mcusgr."code" AS "customer.group.code", mcusgr."label" AS "customer.group.label",
							mcusgr."mtime" AS "customer.group.mtime", mcusgr."editor" AS "customer.group.editor",
							mcusgr."ctime" AS "customer.group.ctime"
						FROM "mshop_customer_group" AS mcusgr
						:joins
						WHERE :cond
						GROUP BY mcusgr."id", mcusgr."siteid", mcusgr."code", mcusgr."label",
							mcusgr."mtime", mcusgr."editor", mcusgr."ctime" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mcusgr."id"
							FROM "mshop_customer_group" AS mcusgr
							:joins
							WHERE :cond
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
							DELETE FROM "mshop_customer_list_type"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_customer_list_type" (
								"code", "domain", "label", "pos", "status",
								"mtime", "editor", "siteid", "ctime"
							) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_customer_list_type"
							SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
								"status" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT mcuslity."id" AS "customer.lists.type.id", mcuslity."siteid" AS "customer.lists.type.siteid",
								mcuslity."code" AS "customer.lists.type.code", mcuslity."domain" AS "customer.lists.type.domain",
								mcuslity."label" AS "customer.lists.type.label", mcuslity."status" AS "customer.lists.type.status",
								mcuslity."mtime" AS "customer.lists.type.mtime", mcuslity."editor" AS "customer.lists.type.editor",
								mcuslity."ctime" AS "customer.lists.type.ctime", mcuslity."pos" AS "customer.lists.type.position"
							FROM "mshop_customer_list_type" AS mcuslity
							:joins
							WHERE :cond
							GROUP BY mcuslity."id", mcuslity."siteid", mcuslity."code", mcuslity."domain",
								mcuslity."label", mcuslity."status", mcuslity."mtime", mcuslity."editor",
								mcuslity."ctime", mcuslity."pos" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						'
					),
					'count' => array(
						'ansi' => '
							SELECT COUNT(*) AS "count"
							FROM (
								SELECT DISTINCT mcuslity."id"
								FROM "mshop_customer_list_type" as mcuslity
								:joins
								WHERE :cond
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
							SELECT :key AS "key", mcusli."id" AS "id"
							FROM "mshop_customer_list" AS mcusli
							:joins
							WHERE :cond
							GROUP BY :key, mcusli."id"
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY "key"
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_customer_list"
						WHERE :cond AND siteid = ?
					'
				),
				'getposmax' => array(
					'ansi' => '
						SELECT MAX( "pos" ) AS pos
						FROM "mshop_customer_list"
						WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
							AND "domain" = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_customer_list" (
							"parentid", "typeid", "domain", "refid", "start", "end",
							"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_customer_list"
						SET "parentid"=?, "typeid" = ?, "domain" = ?, "refid" = ?, "start" = ?, "end" = ?,
							"config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'updatepos' => array(
					'ansi' => '
						UPDATE "mshop_customer_list"
							SET "pos" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'move' => array(
					'ansi' => '
						UPDATE "mshop_customer_list"
							SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
							AND "domain" = ? AND "pos" >= ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mcusli."id" AS "customer.lists.id", mcusli."parentid" AS "customer.lists.parentid",
							mcusli."siteid" AS "customer.lists.siteid", mcusli."typeid" AS "customer.lists.typeid",
							mcusli."domain" AS "customer.lists.domain", mcusli."refid" AS "customer.lists.refid",
							mcusli."start" AS "customer.lists.datestart", mcusli."end" AS "customer.lists.dateend",
							mcusli."config" AS "customer.lists.config", mcusli."pos" AS "customer.lists.position",
							mcusli."status" AS "customer.lists.status", mcusli."mtime" AS "customer.lists.mtime",
							mcusli."editor" AS "customer.lists.editor", mcusli."ctime" AS "customer.lists.ctime"
						FROM "mshop_customer_list" AS mcusli
						:joins
						WHERE :cond
						GROUP BY mcusli."id", mcusli."parentid", mcusli."siteid", mcusli."typeid",
							mcusli."domain", mcusli."refid", mcusli."start", mcusli."end",
							mcusli."config", mcusli."pos", mcusli."status", mcusli."mtime",
							mcusli."editor", mcusli."ctime" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mcusli."id"
							FROM "mshop_customer_list" AS mcusli
							:joins
							WHERE :cond
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
							DELETE FROM "mshop_customer_property_type"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_customer_property_type" (
								"code", "domain", "label", "pos", "status",
								"mtime", "editor", "siteid", "ctime"
							) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_customer_property_type"
							SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
								"status" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT mcusprty."id" AS "customer.property.type.id", mcusprty."siteid" AS "customer.property.type.siteid",
								mcusprty."code" AS "customer.property.type.code", mcusprty."domain" AS "customer.property.type.domain",
								mcusprty."label" AS "customer.property.type.label", mcusprty."status" AS "customer.property.type.status",
								mcusprty."mtime" AS "customer.property.type.mtime", mcusprty."editor" AS "customer.property.type.editor",
								mcusprty."ctime" AS "customer.property.type.ctime", mcusprty."pos" AS "customer.property.type.position"
							FROM "mshop_customer_property_type" mcusprty
							:joins
							WHERE :cond
							GROUP BY mcusprty."id", mcusprty."siteid", mcusprty."code", mcusprty."domain",
								mcusprty."label", mcusprty."status", mcusprty."mtime", mcusprty."editor",
								mcusprty."ctime", mcusprty."pos" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						'
					),
					'count' => array(
						'ansi' => '
							SELECT COUNT(*) AS "count"
							FROM (
								SELECT DISTINCT mcusprty."id"
								FROM "mshop_customer_property_type" mcusprty
								:joins
								WHERE :cond
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
						'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
						'sqlanywhere' => 'SELECT @@IDENTITY',
					),
				),
			),
			'standard' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_customer_property"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_customer_property" (
							"parentid", "typeid", "langid", "value",
							"mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_customer_property"
						SET "parentid" = ?, "typeid" = ?, "langid" = ?,
							"value" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mcuspr."id" AS "customer.property.id", mcuspr."parentid" AS "customer.property.parentid",
							mcuspr."siteid" AS "customer.property.siteid", mcuspr."typeid" AS "customer.property.typeid",
							mcuspr."langid" AS "customer.property.languageid", mcuspr."value" AS "customer.property.value",
							mcuspr."mtime" AS "customer.property.mtime", mcuspr."editor" AS "customer.property.editor",
							mcuspr."ctime" AS "customer.property.ctime"
						FROM "mshop_customer_property" AS mcuspr
						:joins
						WHERE :cond
						GROUP BY mcuspr."id", mcuspr."parentid", mcuspr."siteid", mcuspr."typeid",
							mcuspr."langid", mcuspr."value", mcuspr."mtime", mcuspr."editor",
							mcuspr."ctime" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mcuspr."id"
							FROM "mshop_customer_property" AS mcuspr
							:joins
							WHERE :cond
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
					'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
		),
		'standard' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_customer"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_customer" (
						"siteid", "label", "code", "company", "vatid", "salutation", "title",
						"firstname", "lastname", "address1", "address2", "address3",
						"postal", "city", "state", "countryid", "langid", "telephone",
						"email", "telefax", "website", "longitude", "latitude", "birthday",
						"status", "vdate", "password", "mtime", "editor", "ctime"
					) VALUES (
						?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_customer"
					SET "siteid" = ?, "label" = ?, "code" = ?, "company" = ?, "vatid" = ?,
						"salutation" = ?, "title" = ?, "firstname" = ?, "lastname" = ?,
						"address1" = ?, "address2" = ?, "address3" = ?, "postal" = ?,
						"city" = ?, "state" = ?, "countryid" = ?, "langid" = ?,
						"telephone" = ?, "email" = ?, "telefax" = ?, "website" = ?,
						"longitude" = ?, "latitude" = ?, "birthday" = ?, "status" = ?,
						"vdate" = ?, "password" = ?, "mtime" = ?, "editor" = ?
					WHERE "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT mcus."id" AS "customer.id", mcus."siteid" AS "customer.siteid",
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
					GROUP BY mcus."id", mcus."siteid", mcus."label", mcus."code",
						mcus."company", mcus."vatid", mcus."salutation", mcus."title",
						mcus."firstname", mcus."lastname", mcus."address1", mcus."address2",
						mcus."address3", mcus."postal", mcus."city", mcus."state",
						mcus."countryid", mcus."langid", mcus."telephone", mcus."email",
						mcus."telefax", mcus."website", mcus."longitude", mcus."latitude",
						mcus."birthday", mcus."status", mcus."vdate", mcus."password",
						mcus."ctime", mcus."mtime", mcus."editor"
						/*-columns*/ , :columns /*columns-*/
					/*-orderby*/ ORDER BY :order /*orderby-*/
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT DISTINCT mcus."id"
						FROM "mshop_customer" AS mcus
						:joins
						WHERE :cond
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
				'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
	),
);