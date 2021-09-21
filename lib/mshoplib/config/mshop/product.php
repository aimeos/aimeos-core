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
						DELETE FROM "mshop_product_list_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_product_list_type" ( :names
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_product_list_type"
						SET :names
							"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mprolity."id" AS "product.lists.type.id", mprolity."siteid" AS "product.lists.type.siteid",
							mprolity."code" AS "product.lists.type.code", mprolity."domain" AS "product.lists.type.domain",
							mprolity."label" AS "product.lists.type.label", mprolity."status" AS "product.lists.type.status",
							mprolity."mtime" AS "product.lists.type.mtime", mprolity."editor" AS "product.lists.type.editor",
							mprolity."ctime" AS "product.lists.type.ctime", mprolity."pos" AS "product.lists.type.position"
						FROM "mshop_product_list_type" AS mprolity
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mprolity."id" AS "product.lists.type.id", mprolity."siteid" AS "product.lists.type.siteid",
							mprolity."code" AS "product.lists.type.code", mprolity."domain" AS "product.lists.type.domain",
							mprolity."label" AS "product.lists.type.label", mprolity."status" AS "product.lists.type.status",
							mprolity."mtime" AS "product.lists.type.mtime", mprolity."editor" AS "product.lists.type.editor",
							mprolity."ctime" AS "product.lists.type.ctime", mprolity."pos" AS "product.lists.type.position"
						FROM "mshop_product_list_type" AS mprolity
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
							SELECT mprolity."id"
							FROM "mshop_product_list_type" AS mprolity
							:joins
							WHERE :cond
							ORDER BY mprolity."id"
							OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mprolity."id"
							FROM "mshop_product_list_type" AS mprolity
							:joins
							WHERE :cond
							ORDER BY mprolity."id"
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
					'sqlsrv' => 'SELECT @@IDENTITY',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
			'aggregate' => array(
				'ansi' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :val AS "val"
						FROM "mshop_product_list" AS mproli
						:joins
						WHERE :cond
						GROUP BY :cols, mproli."id"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY :keys
				',
				'mysql' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :val AS "val"
						FROM "mshop_product_list" AS mproli
						:joins
						WHERE :cond
						GROUP BY :cols, mproli."id"
						ORDER BY :order
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_product_list"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_product_list" ( :names
						"parentid", "key", "type", "domain", "refid", "start", "end",
						"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_product_list"
					SET :names
						"parentid" = ?, "key" = ?, "type" = ?, "domain" = ?, "refid" = ?, "start" = ?,
						"end" = ?, "config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mproli."id" AS "product.lists.id", mproli."parentid" AS "product.lists.parentid",
						mproli."siteid" AS "product.lists.siteid", mproli."type" AS "product.lists.type",
						mproli."domain" AS "product.lists.domain", mproli."refid" AS "product.lists.refid",
						mproli."start" AS "product.lists.datestart", mproli."end" AS "product.lists.dateend",
						mproli."config" AS "product.lists.config", mproli."pos" AS "product.lists.position",
						mproli."status" AS "product.lists.status", mproli."mtime" AS "product.lists.mtime",
						mproli."editor" AS "product.lists.editor", mproli."ctime" AS "product.lists.ctime"
					FROM "mshop_product_list" AS mproli
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mproli."id" AS "product.lists.id", mproli."parentid" AS "product.lists.parentid",
						mproli."siteid" AS "product.lists.siteid", mproli."type" AS "product.lists.type",
						mproli."domain" AS "product.lists.domain", mproli."refid" AS "product.lists.refid",
						mproli."start" AS "product.lists.datestart", mproli."end" AS "product.lists.dateend",
						mproli."config" AS "product.lists.config", mproli."pos" AS "product.lists.position",
						mproli."status" AS "product.lists.status", mproli."mtime" AS "product.lists.mtime",
						mproli."editor" AS "product.lists.editor", mproli."ctime" AS "product.lists.ctime"
					FROM "mshop_product_list" AS mproli
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
						SELECT mproli."id"
						FROM "mshop_product_list" AS mproli
						:joins
						WHERE :cond
						ORDER BY mproli."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mproli."id"
						FROM "mshop_product_list" AS mproli
						:joins
						WHERE :cond
						ORDER BY mproli."id"
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
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'property' => array(
			'type' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_product_property_type"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_product_property_type" ( :names
							"code", "domain", "label", "pos", "status",
							"mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_product_property_type"
						SET :names
							"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
							"status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mproprty."id" AS "product.property.type.id", mproprty."siteid" AS "product.property.type.siteid",
							mproprty."code" AS "product.property.type.code", mproprty."domain" AS "product.property.type.domain",
							mproprty."label" AS "product.property.type.label", mproprty."status" AS "product.property.type.status",
							mproprty."mtime" AS "product.property.type.mtime", mproprty."editor" AS "product.property.type.editor",
							mproprty."ctime" AS "product.property.type.ctime", mproprty."pos" AS "product.property.type.position"
						FROM "mshop_product_property_type" mproprty
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mproprty."id" AS "product.property.type.id", mproprty."siteid" AS "product.property.type.siteid",
							mproprty."code" AS "product.property.type.code", mproprty."domain" AS "product.property.type.domain",
							mproprty."label" AS "product.property.type.label", mproprty."status" AS "product.property.type.status",
							mproprty."mtime" AS "product.property.type.mtime", mproprty."editor" AS "product.property.type.editor",
							mproprty."ctime" AS "product.property.type.ctime", mproprty."pos" AS "product.property.type.position"
						FROM "mshop_product_property_type" mproprty
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
							SELECT mproprty."id"
							FROM "mshop_product_property_type" mproprty
							:joins
							WHERE :cond
							ORDER BY mproprty."id"
							OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mproprty."id"
							FROM "mshop_product_property_type" mproprty
							:joins
							WHERE :cond
							ORDER BY mproprty."id"
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
					'sqlsrv' => 'SELECT @@IDENTITY',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_product_property"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_product_property" ( :names
						"parentid", "key", "type", "langid", "value",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_product_property"
					SET :names
						"parentid" = ?, "key" = ?, "type" = ?, "langid" = ?,
						"value" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mpropr."id" AS "product.property.id", mpropr."parentid" AS "product.property.parentid",
						mpropr."siteid" AS "product.property.siteid", mpropr."type" AS "product.property.type",
						mpropr."langid" AS "product.property.languageid", mpropr."value" AS "product.property.value",
						mpropr."mtime" AS "product.property.mtime", mpropr."editor" AS "product.property.editor",
						mpropr."ctime" AS "product.property.ctime"
					FROM "mshop_product_property" AS mpropr
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mpropr."id" AS "product.property.id", mpropr."parentid" AS "product.property.parentid",
						mpropr."siteid" AS "product.property.siteid", mpropr."type" AS "product.property.type",
						mpropr."langid" AS "product.property.languageid", mpropr."value" AS "product.property.value",
						mpropr."mtime" AS "product.property.mtime", mpropr."editor" AS "product.property.editor",
						mpropr."ctime" AS "product.property.ctime"
					FROM "mshop_product_property" AS mpropr
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
						SELECT mpropr."id"
						FROM "mshop_product_property" AS mpropr
						:joins
						WHERE :cond
						ORDER BY mpropr."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mpropr."id"
						FROM "mshop_product_property" AS mpropr
						:joins
						WHERE :cond
						ORDER BY mpropr."id"
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
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'type' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_product_type"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_product_type" ( :names
						"code", "domain", "label", "pos", "status",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_product_type"
					SET :names
						"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
						"status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mproty."id" AS "product.type.id", mproty."siteid" AS "product.type.siteid",
						mproty."code" AS "product.type.code", mproty."domain" AS "product.type.domain",
						mproty."label" AS "product.type.label", mproty."status" AS "product.type.status",
						mproty."mtime" AS "product.type.mtime", mproty."editor" AS "product.type.editor",
						mproty."ctime" AS "product.type.ctime", mproty."pos" AS "product.type.position"
					FROM "mshop_product_type" AS mproty
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mproty."id" AS "product.type.id", mproty."siteid" AS "product.type.siteid",
						mproty."code" AS "product.type.code", mproty."domain" AS "product.type.domain",
						mproty."label" AS "product.type.label", mproty."status" AS "product.type.status",
						mproty."mtime" AS "product.type.mtime", mproty."editor" AS "product.type.editor",
						mproty."ctime" AS "product.type.ctime", mproty."pos" AS "product.type.position"
					FROM "mshop_product_type" AS mproty
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
						SELECT mproty."id"
						FROM "mshop_product_type" AS mproty
						:joins
						WHERE :cond
						ORDER BY mproty."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mproty."id"
						FROM "mshop_product_type" AS mproty
						:joins
						WHERE :cond
						ORDER BY mproty."id"
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
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_product"
				WHERE :cond AND siteid = ?
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_product" ( :names
					"type", "code", "dataset", "label", "url", "instock", "status", "scale",
					"start", "end", "config", "target", "editor", "mtime", "ctime", "siteid"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_product"
				SET :names
					"type" = ?, "code" = ?, "dataset" = ?, "label" = ?, "url" = ?, "instock" = ?,
					"status" = ?, "scale" = ?, "start" = ?, "end" = ?, "config" = ?, "target" = ?,
					"editor" = ?, "mtime" = ?, "ctime" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'rate' => array(
			'ansi' => '
				UPDATE "mshop_product"
				SET "rating" = ?, "ratings" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'stock' => array(
			'ansi' => '
				UPDATE "mshop_product"
				SET "instock" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
					mpro."id" AS "product.id", mpro."siteid" AS "product.siteid",
					mpro."type" AS "product.type", mpro."code" AS "product.code",
					mpro."label" AS "product.label", mpro."url" AS "product.url",
					mpro."start" AS "product.datestart", mpro."end" AS "product.dateend",
					mpro."status" AS "product.status", mpro."ctime" AS "product.ctime",
					mpro."mtime" AS "product.mtime", mpro."editor" AS "product.editor",
					mpro."target" AS "product.target", mpro."dataset" AS "product.dataset",
					mpro."scale" AS "product.scale", mpro."config" AS "product.config",
					mpro."rating" AS "product.rating", mpro."ratings" AS "product.ratings",
					mpro."instock" AS "product.instock"
				FROM "mshop_product" AS mpro
				:joins
				WHERE :cond
				GROUP BY :columns :group
					mpro."id", mpro."siteid", mpro."type", mpro."code", mpro."label", mpro."url",
					mpro."target", mpro."dataset", mpro."scale", mpro."config", mpro."start", mpro."end",
					mpro."status", mpro."ctime", mpro."mtime", mpro."editor", mpro."rating", mpro."ratings",
					mpro."instock"
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					mpro."id" AS "product.id", mpro."siteid" AS "product.siteid",
					mpro."type" AS "product.type", mpro."code" AS "product.code",
					mpro."label" AS "product.label", mpro."url" AS "product.url",
					mpro."start" AS "product.datestart", mpro."end" AS "product.dateend",
					mpro."status" AS "product.status", mpro."ctime" AS "product.ctime",
					mpro."mtime" AS "product.mtime", mpro."editor" AS "product.editor",
					mpro."target" AS "product.target", mpro."dataset" AS "product.dataset",
					mpro."scale" AS "product.scale", mpro."config" AS "product.config",
					mpro."rating" AS "product.rating", mpro."ratings" AS "product.ratings",
					mpro."instock" AS "product.instock"
				FROM "mshop_product" AS mpro
				:joins
				WHERE :cond
				GROUP BY :group mpro."id"
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mpro."id"
					FROM "mshop_product" AS mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY mpro."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mpro."id"
					FROM "mshop_product" AS mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY mpro."id"
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
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
