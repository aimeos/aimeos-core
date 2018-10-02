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
							DELETE FROM "mshop_product_list_type"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_product_list_type" (
								"code", "domain", "label", "pos", "status",
								"mtime", "editor", "siteid", "ctime"
							) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_product_list_type"
							SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
								"status" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT mprolity."id" AS "product.lists.type.id", mprolity."siteid" AS "product.lists.type.siteid",
								mprolity."code" AS "product.lists.type.code", mprolity."domain" AS "product.lists.type.domain",
								mprolity."label" AS "product.lists.type.label", mprolity."status" AS "product.lists.type.status",
								mprolity."mtime" AS "product.lists.type.mtime", mprolity."editor" AS "product.lists.type.editor",
								mprolity."ctime" AS "product.lists.type.ctime", mprolity."pos" AS "product.lists.type.position"
							FROM "mshop_product_list_type" AS mprolity
							:joins
							WHERE :cond
							GROUP BY mprolity."id", mprolity."siteid", mprolity."code", mprolity."domain",
								mprolity."label", mprolity."status", mprolity."mtime", mprolity."editor",
								mprolity."ctime", mprolity."pos" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						'
					),
					'count' => array(
						'ansi' => '
							SELECT COUNT(*) AS "count"
							FROM (
								SELECT DISTINCT mprolity."id"
								FROM "mshop_product_list_type" AS mprolity
								:joins
								WHERE :cond
								LIMIT 10000 OFFSET 0
							) AS list
						'
					),
					'newid' => array(
						'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
						'mysql' => 'SELECT LAST_INSERT_ID()',
						'oracle' => 'SELECT mshop_product_list_type_seq.CURRVAL FROM DUAL',
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
							SELECT DISTINCT :key AS "key", mproli."id" AS "id"
							FROM "mshop_product_list" AS mproli
							:joins
							WHERE :cond
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY "key"
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_product_list"
						WHERE :cond AND siteid = ?
					'
				),
				'getposmax' => array(
					'ansi' => '
						SELECT MAX( "pos" ) AS pos
						FROM "mshop_product_list"
						WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
							AND "domain" = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_product_list" (
							"parentid", "typeid", "domain", "refid", "start", "end",
							"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_product_list"
						SET "parentid" = ?, "typeid" = ?, "domain" = ?, "refid" = ?, "start" = ?, "end" = ?,
							"config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'updatepos' => array(
					'ansi' => '
						UPDATE "mshop_product_list"
						SET "pos" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'move' => array(
					'ansi' => '
						UPDATE "mshop_product_list"
						SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
							AND "domain" = ? AND "pos" >= ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mproli."id" AS "product.lists.id", mproli."parentid" AS "product.lists.parentid",
							mproli."siteid" AS "product.lists.siteid", mproli."typeid" AS "product.lists.typeid",
							mproli."domain" AS "product.lists.domain", mproli."refid" AS "product.lists.refid",
							mproli."start" AS "product.lists.datestart", mproli."end" AS "product.lists.dateend",
							mproli."config" AS "product.lists.config", mproli."pos" AS "product.lists.position",
							mproli."status" AS "product.lists.status", mproli."mtime" AS "product.lists.mtime",
							mproli."editor" AS "product.lists.editor", mproli."ctime" AS "product.lists.ctime"
						FROM "mshop_product_list" AS mproli
						:joins
						WHERE :cond
						GROUP BY mproli."id", mproli."parentid", mproli."siteid", mproli."typeid",
							mproli."domain", mproli."refid", mproli."start", mproli."end",
							mproli."config", mproli."pos", mproli."status", mproli."mtime",
							mproli."editor", mproli."ctime" /*-columns*/ , :columns /*columns-*/
						 /*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mproli."id"
							FROM "mshop_product_list" AS mproli
							:joins
							WHERE :cond
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_product_list_seq.CURRVAL FROM DUAL',
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
							DELETE FROM "mshop_product_property_type"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_product_property_type" (
								"code", "domain", "label", "pos", "status",
								"mtime", "editor", "siteid", "ctime"
							) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_product_property_type"
							SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
								"status" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT mproprty."id" AS "product.property.type.id", mproprty."siteid" AS "product.property.type.siteid",
								mproprty."code" AS "product.property.type.code", mproprty."domain" AS "product.property.type.domain",
								mproprty."label" AS "product.property.type.label", mproprty."status" AS "product.property.type.status",
								mproprty."mtime" AS "product.property.type.mtime", mproprty."editor" AS "product.property.type.editor",
								mproprty."ctime" AS "product.property.type.ctime", mproprty."pos" AS "product.property.type.position"
							FROM "mshop_product_property_type" mproprty
							:joins
							WHERE :cond
							GROUP BY mproprty."id", mproprty."siteid", mproprty."code", mproprty."domain",
								mproprty."label", mproprty."status", mproprty."mtime", mproprty."editor",
								mproprty."ctime", mproprty."pos" /*-columns*/ , :columns /*columns-*/
							/*-orderby*/ ORDER BY :order /*orderby-*/
							LIMIT :size OFFSET :start
						'
					),
					'count' => array(
						'ansi' => '
							SELECT COUNT(*) AS "count"
							FROM (
								SELECT DISTINCT mproprty."id"
								FROM "mshop_product_property_type" mproprty
								:joins
								WHERE :cond
								LIMIT 10000 OFFSET 0
							) AS list
						'
					),
					'newid' => array(
						'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
						'mysql' => 'SELECT LAST_INSERT_ID()',
						'oracle' => 'SELECT mshop_product_property_type_seq.CURRVAL FROM DUAL',
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
						DELETE FROM "mshop_product_property"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_product_property" (
							"parentid", "typeid", "langid", "value",
							"mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_product_property"
						SET "parentid" = ?, "typeid" = ?, "langid" = ?,
							"value" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mpropr."id" AS "product.property.id", mpropr."parentid" AS "product.property.parentid",
							mpropr."siteid" AS "product.property.siteid", mpropr."typeid" AS "product.property.typeid",
							mpropr."langid" AS "product.property.languageid", mpropr."value" AS "product.property.value",
							mpropr."mtime" AS "product.property.mtime", mpropr."editor" AS "product.property.editor",
							mpropr."ctime" AS "product.property.ctime"
						FROM "mshop_product_property" AS mpropr
						:joins
						WHERE :cond
						GROUP BY mpropr."id", mpropr."parentid", mpropr."siteid", mpropr."typeid",
							mpropr."langid", mpropr."value", mpropr."mtime", mpropr."editor",
							mpropr."ctime" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mpropr."id"
							FROM "mshop_product_property" AS mpropr
							:joins
							WHERE :cond
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_product_property_seq.CURRVAL FROM DUAL',
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
						DELETE FROM "mshop_product_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_product_type" (
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_product_type"
						SET "code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mproty."id" AS "product.type.id", mproty."siteid" AS "product.type.siteid",
							mproty."code" AS "product.type.code", mproty."domain" AS "product.type.domain",
							mproty."label" AS "product.type.label", mproty."status" AS "product.type.status",
							mproty."mtime" AS "product.type.mtime", mproty."editor" AS "product.type.editor",
							mproty."ctime" AS "product.type.ctime", mproty."pos" AS "product.type.position"
						FROM "mshop_product_type" AS mproty
						:joins
						WHERE :cond
						GROUP BY mproty."id", mproty."siteid", mproty."code", mproty."domain",
							mproty."label", mproty."status", mproty."mtime", mproty."editor",
							mproty."ctime" /*-columns*/ , :columns /*columns-*/
						/*-orderby*/ ORDER BY :order /*orderby-*/
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT DISTINCT mproty."id"
							FROM "mshop_product_type" AS mproty
							:joins
							WHERE :cond
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_product_type_seq.CURRVAL FROM DUAL',
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
					DELETE FROM "mshop_product"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_product" (
						"typeid", "code", "label", "status", "start", "end",
						"config", "target", "editor", "mtime", "ctime", "siteid"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_product"
					SET "typeid" = ?, "code" = ?, "label" = ?, "status" = ?, "start" = ?, "end" = ?,
					"config" = ?, "target" = ?, "editor" = ?, "mtime" = ?, "ctime" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT mpro."id" AS "product.id", mpro."siteid" AS "product.siteid",
						mpro."typeid" AS "product.typeid", mpro."code" AS "product.code",
						mpro."label" AS "product.label", mpro."config" AS "product.config",
						mpro."start" AS "product.datestart", mpro."end" AS "product.dateend",
						mpro."status" AS "product.status", mpro."ctime" AS "product.ctime",
						mpro."mtime" AS "product.mtime", mpro."editor" AS "product.editor",
						mpro."target" AS "product.target"
					FROM "mshop_product" AS mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id", mpro."siteid", mpro."typeid", mpro."code",
						mpro."label", mpro."config", mpro."start", mpro."end",
						mpro."status", mpro."ctime", mpro."mtime", mpro."editor",
						mpro."target"
						/*-columns*/ , :columns /*columns-*/
					/*-orderby*/ ORDER BY :order /*orderby-*/
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT DISTINCT mpro."id"
						FROM "mshop_product" AS mpro
						:joins
						WHERE :cond
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_product_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
	),
);