<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


return array(
	'manager' => array(
		'currency' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_locale_currency" WHERE :cond
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_locale_currency" ( :names
						"label", "status", "mtime", "editor", "id", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_locale_currency"
					SET :names
						"label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mloccu."id" AS "locale.currency.id", mloccu."label" AS "locale.currency.label",
						mloccu."status" AS "locale.currency.status", mloccu."mtime" AS "locale.currency.mtime",
						mloccu."editor" AS "locale.currency.editor", mloccu."ctime" AS "locale.currency.ctime"
					FROM "mshop_locale_currency" AS mloccu
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mloccu."id" AS "locale.currency.id", mloccu."label" AS "locale.currency.label",
						mloccu."status" AS "locale.currency.status", mloccu."mtime" AS "locale.currency.mtime",
						mloccu."editor" AS "locale.currency.editor", mloccu."ctime" AS "locale.currency.ctime"
					FROM "mshop_locale_currency" AS mloccu
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mloccu."id"
						FROM "mshop_locale_currency" AS mloccu
						WHERE :cond
						ORDER BY mloccu."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mloccu."id"
						FROM "mshop_locale_currency" AS mloccu
						WHERE :cond
						ORDER BY mloccu."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_locale_currency_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'language' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_locale_language" WHERE :cond
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_locale_language" ( :names
						"label", "status", "mtime", "editor", "id", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_locale_language"
					SET :names
						"label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mlocla."id" AS "locale.language.id", mlocla."label" AS "locale.language.label",
						mlocla."status" AS "locale.language.status", mlocla."mtime" AS "locale.language.mtime",
						mlocla."editor" AS "locale.language.editor", mlocla."ctime" AS "locale.language.ctime"
					FROM "mshop_locale_language" AS mlocla
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mlocla."id" AS "locale.language.id", mlocla."label" AS "locale.language.label",
						mlocla."status" AS "locale.language.status", mlocla."mtime" AS "locale.language.mtime",
						mlocla."editor" AS "locale.language.editor", mlocla."ctime" AS "locale.language.ctime"
					FROM "mshop_locale_language" AS mlocla
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mlocla."id"
						FROM "mshop_locale_language" AS mlocla
						WHERE :cond
						ORDER BY mlocla."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mlocla."id"
						FROM "mshop_locale_language" AS mlocla
						WHERE :cond
						ORDER BY mlocla."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_locale_language_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'site' => array(
			'cleanup' => [
				'shop' => [
					'domains' => [
						'attribute' => 'attribute',
						'catalog' => 'catalog',
						'coupon' => 'coupon',
						'customer' => 'customer',
						'index' => 'index',
						'media' => 'media',
						'order' => 'order',
						'plugin' => 'plugin',
						'price' => 'price',
						'product' => 'product',
						'review' => 'review',
						'rule' => 'rule',
						'tag' => 'tag',
						'service' => 'service',
						'stock' => 'stock',
						'subscription' => 'subscription',
						'supplier' => 'supplier',
						'text' => 'text'
					]
				],
				'admin' => [
					'domains' => [
						'job' => 'job',
						'log' => 'log',
						'cache' => 'cache'
					]
				]
			],
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_locale_site"
					WHERE :cond
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_locale_site" ( :names
						"siteid", "code", "label", "config", "status", "icon", "logo",
						"supplierid", "theme", "editor", "mtime", "ctime", "parentid", "level",
						"nleft", "nright"

					)
					SELECT :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0,
						COALESCE( MAX("nright"), 0 ) + 1, COALESCE( MAX("nright"), 0 ) + 2
					FROM "mshop_locale_site"
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_locale_site"
					SET :names
						"siteid" = ?, "code" = ?, "label" = ?, "config" = ?, "status" = ?,
						"icon" = ?, "logo" = ?, "supplierid" = ?, "theme" = ?, "editor" = ?, "mtime" = ?
					WHERE id = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mlocsi."id" AS "locale.site.id", mlocsi."siteid" AS "locale.site.siteid",
						mlocsi."code" AS "locale.site.code", mlocsi."label" AS "locale.site.label",
						mlocsi."config" AS "locale.site.config", mlocsi."status" AS "locale.site.status",
						mlocsi."icon" AS "locale.site.icon", mlocsi."logo" AS "locale.site.logo",
						mlocsi."supplierid" AS "locale.site.supplierid", mlocsi."theme" AS "locale.site.theme",
						mlocsi."editor" AS "locale.site.editor", mlocsi."mtime" AS "locale.site.mtime",
						mlocsi."ctime" AS "locale.site.ctime"
					FROM "mshop_locale_site" AS mlocsi
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mlocsi."id" AS "locale.site.id", mlocsi."siteid" AS "locale.site.siteid",
						mlocsi."code" AS "locale.site.code", mlocsi."label" AS "locale.site.label",
						mlocsi."config" AS "locale.site.config", mlocsi."status" AS "locale.site.status",
						mlocsi."icon" AS "locale.site.icon", mlocsi."logo" AS "locale.site.logo",
						mlocsi."supplierid" AS "locale.site.supplierid", mlocsi."theme" AS "locale.site.theme",
						mlocsi."editor" AS "locale.site.editor", mlocsi."mtime" AS "locale.site.mtime",
						mlocsi."ctime" AS "locale.site.ctime"
					FROM "mshop_locale_site" AS mlocsi
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mlocsi."id"
						FROM "mshop_locale_site" AS mlocsi
						WHERE :cond
						ORDER BY mlocsi."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mlocsi."id"
						FROM "mshop_locale_site" AS mlocsi
						WHERE :cond
						ORDER BY mlocsi."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_locale_site_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_locale"
				WHERE :cond AND siteid = ?
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_locale" ( :names
					"langid", "currencyid", "pos", "status",
					"mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_locale"
				SET :names
					"langid" = ?, "currencyid" = ?, "pos" = ?,
					"status" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
					mloc."id" AS "locale.id", mloc."siteid" AS "locale.siteid",
					mloc."langid" AS "locale.languageid", mloc."currencyid" AS "locale.currencyid",
					mloc."pos" AS "locale.position", mloc."status" AS "locale.status",
					mloc."mtime" AS "locale.mtime", mloc."editor" AS "locale.editor",
					mloc."ctime" AS "locale.ctime"
				FROM "mshop_locale" AS mloc
				LEFT JOIN "mshop_locale_site" AS mlocsi ON (mloc."siteid" = mlocsi."siteid")
				LEFT JOIN "mshop_locale_language" AS mlocla ON (mloc."langid" = mlocla."id")
				LEFT JOIN "mshop_locale_currency" AS mloccu ON (mloc."currencyid" = mloccu."id")
				WHERE :cond
				GROUP BY :columns :group
					mloc."id", mloc."siteid", mloc."langid", mloc."currencyid", mloc."pos",
					mloc."status", mloc."mtime", mloc."editor", mloc."ctime"
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					mloc."id" AS "locale.id", mloc."siteid" AS "locale.siteid",
					mloc."langid" AS "locale.languageid", mloc."currencyid" AS "locale.currencyid",
					mloc."pos" AS "locale.position", mloc."status" AS "locale.status",
					mloc."mtime" AS "locale.mtime", mloc."editor" AS "locale.editor",
					mloc."ctime" AS "locale.ctime"
				FROM "mshop_locale" AS mloc
				LEFT JOIN "mshop_locale_site" AS mlocsi ON (mloc."siteid" = mlocsi."siteid")
				LEFT JOIN "mshop_locale_language" AS mlocla ON (mloc."langid" = mlocla."id")
				LEFT JOIN "mshop_locale_currency" AS mloccu ON (mloc."currencyid" = mloccu."id")
				WHERE :cond
				GROUP BY :group mloc."id"
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mloc."id"
					FROM "mshop_locale" AS mloc
					LEFT JOIN "mshop_locale_site" AS mlocsi ON (mloc."siteid" = mlocsi."siteid")
					LEFT JOIN "mshop_locale_language" AS mlocla ON (mloc."langid" = mlocla."id")
					LEFT JOIN "mshop_locale_currency" AS mloccu ON (mloc."currencyid" = mloccu."id")
					WHERE :cond
					GROUP BY mloc."id"
					ORDER BY mloc."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mloc."id"
					FROM "mshop_locale" AS mloc
					LEFT JOIN "mshop_locale_site" AS mlocsi ON (mloc."siteid" = mlocsi."siteid")
					LEFT JOIN "mshop_locale_language" AS mlocla ON (mloc."langid" = mlocla."id")
					LEFT JOIN "mshop_locale_currency" AS mloccu ON (mloc."currencyid" = mloccu."id")
					WHERE :cond
					GROUP BY mloc."id"
					ORDER BY mloc."id"
					LIMIT 10000 OFFSET 0
				) AS list
			'
		),
		'newid' => array(
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT mshop_locale_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
