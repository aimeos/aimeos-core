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
							DELETE FROM "mshop_price_list_type"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_price_list_type" (
								"code", "domain", "label", "pos", "status",
								"mtime", "editor", "siteid", "ctime"
							) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_price_list_type"
							SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
								"status" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT mprility."id" AS "price.lists.type.id", mprility."siteid" AS "price.lists.type.siteid",
								mprility."code" AS "price.lists.type.code", mprility."domain" AS "price.lists.type.domain",
								mprility."label" AS "price.lists.type.label", mprility."status" AS "price.lists.type.status",
								mprility."mtime" AS "price.lists.type.mtime", mprility."editor" AS "price.lists.type.editor",
								mprility."ctime" AS "price.lists.type.ctime", mprility."pos" AS "price.lists.type.position"
							FROM "mshop_price_list_type" AS mprility
							:joins
							WHERE :cond
							GROUP BY mprility."id", mprility."siteid", mprility."code", mprility."domain",
								mprility."label", mprility."status", mprility."mtime", mprility."editor",
								mprility."ctime", mprility."pos" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						'
					),
					'count' => array(
						'ansi' => '
							SELECT COUNT(*) AS "count"
							FROM (
								SELECT DISTINCT mprility."id"
								FROM "mshop_price_list_type" AS mprility
								:joins
								WHERE :cond
								LIMIT 10000 OFFSET 0
							) AS list
						'
					),
					'newid' => array(
						'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
						'mysql' => 'SELECT LAST_INSERT_ID()',
						'oracle' => 'SELECT mshop_price_list_type_seq.CURRVAL FROM DUAL',
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
							SELECT :key AS "key", mprili."id" AS "id"
							FROM "mshop_price_list" AS mprili
							:joins
							WHERE :cond
							GROUP BY :key, mprili."id" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY "key"
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_price_list"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_price_list" (
							"parentid", "key", "type", "domain", "refid", "start", "end",
							"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_price_list"
						SET "parentid"=?, "key" = ?, "type" = ?, "domain" = ?, "refid" = ?, "start" = ?, "end" = ?,
							"config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mprili."id" AS "price.lists.id", mprili."parentid" AS "price.lists.parentid",
							mprili."siteid" AS "price.lists.siteid", mprili."type" AS "price.lists.type",
							mprili."domain" AS "price.lists.domain", mprili."refid" AS "price.lists.refid",
							mprili."start" AS "price.lists.datestart", mprili."end" AS "price.lists.dateend",
							mprili."config" AS "price.lists.config", mprili."pos" AS "price.lists.position",
							mprili."status" AS "price.lists.status", mprili."mtime" AS "price.lists.mtime",
							mprili."editor" AS "price.lists.editor", mprili."ctime" AS "price.lists.ctime"
						FROM "mshop_price_list" AS mprili
						:joins
						WHERE :cond
						GROUP BY mprili."id", mprili."parentid", mprili."siteid", mprili."type",
							mprili."domain", mprili."refid", mprili."start", mprili."end",
							mprili."config", mprili."pos", mprili."status", mprili."mtime",
							mprili."editor", mprili."ctime" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mprili."id"
							FROM "mshop_price_list" AS mprili
							:joins
							WHERE :cond
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_price_list_seq.CURRVAL FROM DUAL',
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
							DELETE FROM "mshop_price_property_type"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_price_property_type" (
								"code", "domain", "label", "pos", "status",
								"mtime", "editor", "siteid", "ctime"
							) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_price_property_type"
							SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
								"status" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT mpriprty."id" AS "price.property.type.id", mpriprty."siteid" AS "price.property.type.siteid",
								mpriprty."code" AS "price.property.type.code", mpriprty."domain" AS "price.property.type.domain",
								mpriprty."label" AS "price.property.type.label", mpriprty."status" AS "price.property.type.status",
								mpriprty."mtime" AS "price.property.type.mtime", mpriprty."editor" AS "price.property.type.editor",
								mpriprty."ctime" AS "price.property.type.ctime", mpriprty."pos" AS "price.property.type.position"
							FROM "mshop_price_property_type" mpriprty
							:joins
							WHERE :cond
							GROUP BY mpriprty."id", mpriprty."siteid", mpriprty."code", mpriprty."domain",
								mpriprty."label", mpriprty."status", mpriprty."mtime", mpriprty."editor",
								mpriprty."ctime", mpriprty."pos" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						'
					),
					'count' => array(
						'ansi' => '
							SELECT COUNT(*) AS "count"
							FROM (
								SELECT DISTINCT mpriprty."id"
								FROM "mshop_price_property_type" mpriprty
								:joins
								WHERE :cond
								LIMIT 10000 OFFSET 0
							) AS list
						'
					),
					'newid' => array(
						'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
						'mysql' => 'SELECT LAST_INSERT_ID()',
						'oracle' => 'SELECT mshop_price_property_type_seq.CURRVAL FROM DUAL',
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
						DELETE FROM "mshop_price_property"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_price_property" (
							"parentid", "key", "type", "langid", "value",
							"mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_price_property"
						SET "parentid" = ?, "key" = ?, "type" = ?, "langid" = ?,
							"value" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mpripr."id" AS "price.property.id", mpripr."parentid" AS "price.property.parentid",
							mpripr."siteid" AS "price.property.siteid", mpripr."type" AS "price.property.type",
							mpripr."langid" AS "price.property.languageid", mpripr."value" AS "price.property.value",
							mpripr."mtime" AS "price.property.mtime", mpripr."editor" AS "price.property.editor",
							mpripr."ctime" AS "price.property.ctime"
						FROM "mshop_price_property" AS mpripr
						:joins
						WHERE :cond
						GROUP BY mpripr."id", mpripr."parentid", mpripr."siteid", mpripr."type",
							mpripr."langid", mpripr."value", mpripr."mtime", mpripr."editor",
							mpripr."ctime" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mpripr."id"
							FROM "mshop_price_property" AS mpripr
							:joins
							WHERE :cond
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_price_property_seq.CURRVAL FROM DUAL',
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
						DELETE FROM "mshop_price_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_price_type" (
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_price_type"
						SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mprity."id" AS "price.type.id", mprity."siteid" AS "price.type.siteid",
							mprity."code" AS "price.type.code", mprity."domain" AS "price.type.domain",
							mprity."label" AS "price.type.label", mprity."status" AS "price.type.status",
							mprity."mtime" AS "price.type.mtime", mprity."editor" AS "price.type.editor",
							mprity."ctime" AS "price.type.ctime", mprity."pos" AS "price.type.position"
						FROM "mshop_price_type" AS mprity
						:joins
						WHERE :cond
						GROUP BY mprity."id", mprity."siteid", mprity."code", mprity."domain",
							mprity."label", mprity."status", mprity."mtime", mprity."editor",
							mprity."ctime", mprity."pos" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mprity."id"
							FROM "mshop_price_type" AS mprity
							:joins
							WHERE :cond
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_price_type_seq.CURRVAL FROM DUAL',
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
					DELETE FROM "mshop_price"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_price" (
						"type", "currencyid", "domain", "label",
						"quantity", "value", "costs", "rebate", "taxrate",
						"status", "mtime", "editor", "siteid", "ctime"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_price"
					SET "type" = ?, "currencyid" = ?, "domain" = ?, "label" = ?,
						"quantity" = ?, "value" = ?, "costs" = ?, "rebate" = ?,
						"taxrate" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT mpri."id" AS "price.id", mpri."siteid" AS "price.siteid",
						mpri."type" AS "price.type", mpri."currencyid" AS "price.currencyid",
						mpri."domain" AS "price.domain", mpri."label" AS "price.label",
						mpri."quantity" AS "price.quantity", mpri."value" AS "price.value",
						mpri."costs" AS "price.costs", mpri."rebate" AS "price.rebate",
						mpri."taxrate" AS "price.taxrates", mpri."status" AS "price.status",
						mpri."mtime" AS "price.mtime", mpri."editor" AS "price.editor",
						mpri."ctime" AS "price.ctime"
					FROM "mshop_price" AS mpri
					:joins
					WHERE :cond
					GROUP BY mpri."id", mpri."siteid", mpri."type", mpri."currencyid",
						mpri."domain", mpri."label", mpri."quantity", mpri."value",
						mpri."costs", mpri."rebate", mpri."taxrate", mpri."status",
						mpri."mtime", mpri."editor", mpri."ctime" /*-columns*/ , :columns /*columns-*/
					/*-orderby*/ ORDER BY :order /*orderby-*/
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT DISTINCT mpri."id"
						FROM "mshop_price" AS mpri
						:joins
						WHERE :cond
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_price_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
	),
);
